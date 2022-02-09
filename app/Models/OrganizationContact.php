<?php

namespace App\Models;

use App\Exceptions\AppException;
use App\Models\Base\BaseModel;
use App\Utils\DateTimeUtil;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrganizationContact extends MasterModel
{

    protected $table = 'organization_contacts';

    protected $hidden = [
        'verification_token',
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "deleted_at",
        "deleted_by"
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public static function rules($id = null)
    {
        return [
            'email' => 'required|email',
            'name' => 'required|string|max:100',
            'status' => 'boolean',
            'is_primary' => 'boolean',
            'verified_at' => 'nullable|integer',
            'verification_token' => 'required|string',
            'organization_id' => 'required|integer|exists:organizations,id',
        ];
    }

    public static function inst()
    {
        return new OrganizationContact();
    }

    public function populate($request = array(), BaseModel $model = null): OrganizationContact
    {

        $req = new Collection($request);

        if (is_null($model)) {
            $model = self::inst();

            $model->email = $req->get("email");
            $model->name = $req->get("name");
            $model->status = (!empty($req->get("status"))) ? $req->get("status") : true;
            $model->verified_at = (!empty($req->get("verified_at"))) ? $req->get("verified_at") : null;
            $model->is_primary = (!empty($req->get("is_primary"))) ? $req->get("is_primary") : false;
            $model->organization_id = $req->get("organization_id");
            $model->verification_token = str_random(40) . DateTimeUtil::currentMicroSecond();
        }

        $model->name = $req->get("name");

//        'verification_token' => '',
//        'verified_at' => '',
//        'is_primary' => false,

        return $model;

    }

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
                ->where("name", "ILIKE", "%" . $query . "%");
        }

        return $data;
    }

    public function scopeGetByToken($q, $token)
    {
        return $q->where('verification_token', $token)->first();
    }

    public function scopeGetByEmail($q, $email)
    {
        return $q->where('email', $email)->first();
    }

    public function scopeGetByOrgRef($q, $orgId)
    {
        return $q->where('organization_id', $orgId);
    }

    public function scopeGetByOrgAndIdRef($q, $orgId, $id)
    {
        return $q->where('organization_id', $orgId)->where('id', $id);
    }

    public function destroyExec($id, $orgId = null)
    {
        DB::beginTransaction();
        try {
            $dataInId = $this->getByOrgAndIdRef($orgId, $id)->firstOrFail();

            if (!$dataInId->delete()) {
                DB::rollback();
            }
            DB::commit();
            return $dataInId;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function verifyTokenExec(array $arrReq)
    {
        try {
            $data = $this->getByOrgRef($arrReq['org'])->getByToken($arrReq['token']);

            if ($data) {
                if ($data->verified_at) {

                    throw  AppException::inst('Your contact email is verified.', Response::HTTP_OK);
                }

                $data->verified_at = DateTimeUtil::currentMicroSecond();

                if (!$data->save()) {
                    throw  AppException::inst('verification email failed.', Response::HTTP_INTERNAL_SERVER_ERROR, $data->errors);
                }

                return true;
            }

            throw AppException::inst('Data Not Found.', Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            throw $e;
        }
    }
}