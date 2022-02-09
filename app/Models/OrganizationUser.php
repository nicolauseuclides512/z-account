<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class OrganizationUser extends MasterModel
{
    protected $table = 'organization_users';

    protected $fillable = [
        'user_id',
        'organization_id',
        'role_id',
        'status'
    ];

    const STATUS = [
        'ACTIVE' => 1,
        'INACTIVE' => 2,
        'DELETED' => -1,
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    protected static $rules = array(
        'organization_id' => 'required|integer|exists:organizations,id',
        'user_id' => 'required|integer|exists:users,id',
        'is_primary' => 'boolean',
        'scope_id' => 'nullable|integer|exists:scopes,id'
    );


    public static function inst()
    {
        return new OrganizationUser();
    }

    public function populate($request = array(), BaseModel $model = null)
    {

        if (is_null($model))
            $model = self::inst();

        $req = new Collection($request);

        $model->organization_id = $req->get('organization_id');
        $model->user_id = (Auth::User()) ? Auth::User()->id : $req->get('user_id');
        $model->is_primary = ($req->get('is_primary')) ? $req->get('is_primary') : false;

        //if null should error
        $model->role_id = ($req->get('role_id')) ?: null;
        $model->status = ($req->get('status')) ?: self::STATUS['ACTIVE'];

        return $model;
    }

    public function scopeFilter($q, $filterBy = "", $query = "")
    {
        $data = $q;

        if (!empty($query)) {
            $data = $data
                ->where("name", "ILIKE", "%" . $query . "%");
        }

        return $data;
    }


    /**
     * check existing invitation record
     *
     * @param $userId
     * @param $organizationId
     * @return bool
     */
    public function isUserInOrganizationExist($userId, $organizationId)
    {
        return $this->where('user_id', $userId)
                ->where('organization_id', $organizationId)
                ->count() > 0;
    }


}