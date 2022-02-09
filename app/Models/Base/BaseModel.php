<?php
/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

namespace App\Models\Base;

use App\Exceptions\AppException;
use App\Models\Traits\AppendAttributeTrait;
use App\Models\Traits\ObserveModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

abstract class BaseModel extends Model implements BaseModelContract
{
    use ObserveModelTrait,
        DefaultDAO,
        SoftDeletes,
        AppendAttributeTrait;

    const STATUS_ALL = 9;
    const STATUS_DELETED = -1;
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $hidden = [
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $model;

    protected $fillable = array('created_by', 'updated_by', 'deleted_by');

    protected $dateFormat = 'U';

    protected $softCascades = [];

    protected static $autoValidate = true;

    protected $statusColumn = 'status';

    protected $filterNameCfg = '';

    protected $nestedBelongConfigs = [];

    protected $nestedHasManyConfigs = [];


    public function getSoftCascades()
    {
        return $this->softCascades;
    }

    public function filterCfg()
    {
        return Config::get(empty($this->filterNameCfg)
            ? "filters." . $this->getTable()
            : "filters." . $this->filterNameCfg);
    }

    public function scopeGetByIdRef($q, $id)
    {
        return $q->where('id', $id);
    }

    public function scopeIsExist($q, $id)
    {
        return $q->getByIdRef($id)->count() > 0 ? true : false;
    }

    public function getByStatus($status)
    {
        return $this->where($this->statusColumn, $status);
    }

    public function scopeStatus($q, $status = true)
    {
        return $q->getByStatus($status);
    }

    public static function rules($id = null)
    {
        return [];
    }

    public function populate($request, BaseModel $model = null)
    {
        return $model;
    }

    public function scopeFilter($q, $filterBy = "", $query = "")
    {
        return $q;
    }

    /**
     * @param $q
     * @return mixed
     */
    public function scopeNested($q)
    {
        if (!empty($this->nestedBelongConfigs)) {
            $configs = $this->nestedBelongConfigs;
            $result = array_map(function ($k, $v) {
                return array(
                    $k => function ($q) use ($v) {
                        $q->addSelect($v);
                    }
                );
            }, array_keys($configs), array_values($configs));
            $q = $q->with(call_user_func_array("array_merge", $result));
        }

        if (!empty($this->nestedHasManyConfigs)) {

            $configs = $this->nestedHasManyConfigs;
            $result = array_map(function ($k, $v) {
                return array(
                    $k => function ($q) use ($v) {
                        $q->addSelect($v);
                    }
                );
            }, array_keys($configs), array_values($configs));
            $q = $q->with(call_user_func_array("array_merge", $result));
        }

        return $q;
    }

    /**
     * @return $this
     * @throws AppException
     */
    public function storeOrFail()
    {
        if (!$this->save())
            throw AppException::flash(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                "Save {$this->table} data failure.",
                $this->errors);

        return $this;
    }

}
