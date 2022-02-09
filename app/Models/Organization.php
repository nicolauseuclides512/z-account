<?php

namespace App\Models;

use App\Exceptions\AppException;
use App\Models\Base\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class Organization
 * @package App\Models
 */
class Organization extends MasterModel
{

    /**
     * @var string
     */
    protected $table = 'organizations';

    /**
     * @var string
     */
    protected $filterNameCfg = 'organization';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'logo',
        'address',
        'phone',
        'fax',
        'status',
        'region_id',
        'district_id',
        'province_id',
        'country_id',
        'zip',
        'currency_id',
        'timezone_id',
        'application_id',
        'portal',
    ];

//    protected $guarded = ['portal'];

    /**
     * @var array
     */
    protected $appends = ['multi_res_logo', 'unverified_contact_size', 'primary_contact'];

    /**
     * @var array
     */
    protected $softCascades = ['organizationUser'];

    /**
     * @return MultiResImage|null
     */
    public function getMultiResLogoAttribute()
    {
        if (!$this->endsWith($this->logo, '.jpg') &&
            !$this->endsWith($this->logo, '.jpeg') &&
            !$this->endsWith($this->logo, '.png')) {
            return null;
        }
        $multiRes = new MultiResImage($this->logo);
        return $multiRes;
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organizationUser()
    {
        return $this->hasMany(OrganizationUser::class, 'organization_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organizationUsers()
    {
        return $this->hasMany(OrganizationUser::class, 'organization_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_users', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organizationContact()
    {
        return $this->hasMany(OrganizationContact::class, 'organization_id');
    }

    /**
     * @param null $id
     * @return array
     */
    public static function rules($id = null)
    {
        return [
            'name' => 'required|max:100',
            'logo' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|min:9|max:255',
            'fax' => 'nullable|string|max:30',
            'portal' => 'string|max:30',
            'status' => 'boolean',
            'region_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'province_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'zip' => 'nullable|string|min:5|max:5|regex:/^\+?[^a-zA-Z]{5,}$/',
            'currency_id' => 'nullable|integer',
            'timezone_id' => 'nullable|integer',
            'application_id' => 'sometimes|nullable|integer|exists:applications,id'
        ];
    }

    /**
     * @return MasterModel|Organization
     */
    public static function inst()
    {
        return new Organization();
    }

    /**
     * @param array $request
     * @param BaseModel|null $model
     * @return BaseModel|MasterModel|Organization
     */
    public function populate($request = [], BaseModel $model = null)
    {

        $req = new Collection($request);
        if (is_null($model)) {
            $model = self::inst();
            $model->portal = str_slug($req->get("portal"))
                ?? str_slug($req->get("name")) . "-" . uniqid();
            $model->status = true;
        }

        $model->name = $req->get("name");
        $model->address = $req->get("address");
        $model->zip = $req->get("zip");
        $model->country_id = $req->get("country_id");
        $model->province_id = $req->get("province_id");
        $model->district_id = $req->get("district_id");
        $model->region_id = $req->get("region_id");
        $model->currency_id = $req->get("currency_id");
        $model->timezone_id = $req->get("timezone_id");
//        $model->logo = $req->get("logo");
        $model->fax = $req->get("fax");
        $model->phone = $req->get("phone");
        // memeriksa apakah portal sudah digunakan
        if ($req->get("portal") != null) {
            // 1. get organization
            $organization = organization::where('portal', str_slug($req->get("portal"), "-"))->first();
            // 2. pengecekan
            if (sizeof($organization) == 0) {
                $model->portal = str_slug($req->get("portal")) ?? str_slug($req->get("portal"), "-");
            }
        }

        if ($req->get("action") === 'store')
            $model->application_id = $req->get("application_id") ?? null;

        return $model;
    }

    /**
     * @param $q
     * @param string $filterBy
     * @param string $query
     * @return mixed
     */
    public function scopeFilter($q, $filterBy = "", $query = "")
    {
        $data = $q;

        switch ($filterBy) {
            case self::STATUS_INACTIVE:
                $data = $data->where("status", false);
                break;
            case self::STATUS_ACTIVE:
                $data = $data->where("status", true);
                break;
        }

        if (!empty($query)) {
            $data = $data
                ->where("name", "ILIKE", "%" . $query . "%")
                ->orWhere('created_by', "ILIKE", "%$query%");
        }

        return $data;
    }

    /**
     * @return mixed
     */
    public function getUnverifiedContactSizeAttribute()
    {
        return $this->attributes['unverified_contact_size'] = $this->organizationContact()->whereNull('verified_at')->count();
    }

    /**
     * @return null
     */
    public function getPrimaryContactAttribute()
    {
        $primaryContact = $this->organizationContact()->where('is_primary', true)->first();
        return $this->attributes['primary_contact'] = ($primaryContact) ? $primaryContact : null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function checkNameAvailability($name)
    {
        return $this->where('portal', 'ILIKE', $name)->count() > 0 ? true : false;
    }

    /**
     * @param $request
     * @return BaseModel|MasterModel|Organization
     * @throws Exception
     */
    public function storeExec($request)
    {
        DB::beginTransaction();
        try {

            $request['action'] = 'store';

            $org = $this->populate($request);

            if (!$org->save()) {
                DB::rollback();
                throw AppException::flash(
                    Response::HTTP_BAD_REQUEST,
                    "Error save organization",
                    $org->errors
                );
            }

            $orgUser = OrganizationUser::inst()
                ->populate(['organization_id' => $org->id]);

            if (!$orgUser->save()) {
                DB::rollback();
                throw new Exception($orgUser->errors, Response::HTTP_BAD_REQUEST);
            }

            $user = Auth::User();

            $organizationContact = OrganizationContact::inst()
                ->populate([
                    'email' => $user->email,
                    'name' => $user->username,
                    'status' => true,
                    'is_primary' => true,
                    'organization_id' => $org->id
                ]);

            if (!$organizationContact->save()) {
                DB::rollback();
                throw AppException::flash(
                    Response::HTTP_BAD_REQUEST,
                    "Fail to save contact",
                    $organizationContact->errors);
            }

            DB::commit();
            return $org;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @param $q
     * @return mixed
     */
    public function scopeGetInUser($q)
    {
        return $q->whereHas('organizationUser', function ($q) {
            return $q->where('organization_users.user_id', Auth::User()->id);
        });
    }

    /**
     * @param $request
     * @param $id
     * @return BaseModel|MasterModel|Organization
     * @throws Exception
     */
    public function updateExec($request, $id)
    {
        DB::beginTransaction();
        try {

            if (!Auth::user()->hasOrganization($id)) {
                throw AppException::inst(
                    'Unauthorize organization',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $request['action'] = 'update';
            $dataInId = $this->getByIdRef($id)->firstOrFail();
            $data = $this->populate($request, $dataInId);
            if (!$data->save()) {
                DB::rollback();
                throw AppException::flash(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    trans('messages.organization_profile_update_failed'),
                    $data->errors
                );
            }
            DB::commit();
            return $data;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * get users in organization
     *
     * @return mixed reference
     */
    public function getUsersInOrganizationRef()
    {
        self::$withoutAppends = true;

        return $this->organizationUsers()
            ->with(['user' => function ($q) {
                return $q->select('id', 'username', 'email');
            }])
            ->with(['role' => function ($q) {
                return $q->select('id', 'name');
            }])
            ->where('user_id', '<>', Auth::Id());
    }

    public function scopeGetOrgUserByPortal($q, $portal)
    {
        $org = $q->where('portal', $portal)
            ->with(['organizationUser' => function ($q) {
                return $q->where('role_id', 1)->with('user');
            }])->first();

        return $org;
    }

}
