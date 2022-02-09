<?php

namespace App\Http\Controllers;

use App\Exceptions\AppException;
use App\Http\Controllers\Base\BaseController;
use App\Mail\VerificationContact;
use App\Models\Organization;
use App\Models\OrganizationContact;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrganizationContactController extends BaseController
{
    public $name = 'Asset Organization Contact';

    public $sortBy = ['id', 'name', 'created_at', 'updated_at'];

    public function __construct(Request $request)
    {
        parent::__construct(OrganizationContact::inst(), $request);
    }

    public function index($orgId = null)
    {
        if ($this->request->expectsJson()) {

            if (is_null($orgId) || !Organization::inst()->isExist($orgId)) {
                throw new Exception('param organization id or organization not found.', Response::HTTP_BAD_REQUEST);
            }

            $data = $this->model
                ->getByOrgRef($orgId)
                ->filter($this->requestMod()['filter_by'], $this->requestMod()['q'])
                ->orderBy(
                    $this->requestMod()['sort_column'],
                    $this->requestMod()['sort_order'])
                ->paginate($this->request->input("per_page"));

            return $this->json(Response::HTTP_OK, 'success', $data);
        }

        throw AppException::inst('header not valid.', Response::HTTP_BAD_REQUEST);
    }

    public function show($id, $orgId = null)
    {
        try {
            if ($this->request->expectsJson()) {

                if (is_null($orgId) || !Organization::inst()->isExist($orgId)) {
                    throw new Exception('param organization id or organization not found.', Response::HTTP_BAD_REQUEST);
                }

                return $this->json(Response::HTTP_OK, 'success',
                    $this->model
                        ->getByOrgAndIdRef($orgId, (int)$id)
                        ->firstOrFail());
            }

            throw AppException::inst('header not valid.', Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function store($orgId = null)
    {
        try {

            $request = $this->request->input();

            if (is_null($orgId) || !Organization::inst()->isExist($orgId)) {
                throw new Exception('param organization id or organization not found.', Response::HTTP_BAD_REQUEST);
            }

            $request['organization_id'] = $orgId;

            $data = $this->model->storeExec($request);

            if (isset($data->errors) || isset($data->errorInfo)) {
                throw AppException::inst("Save $this->name is failed.", Response::HTTP_BAD_REQUEST, $data);
            }

            Mail::to($data->email)
                ->send(
                    new VerificationContact(
                        'Verification Contact',
                        htmlspecialchars(url('/verify_contact?org=') . $orgId . '&token=' . $data->verification_token, ENT_NOQUOTES),
                        Auth::User()
                    ));

            return $this->json(Response::HTTP_CREATED, "Save $this->name is successfully.", $data);
        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    protected function update($id, $orgId = null)
    {
        try {
            $request = $this->request->input();

            if (is_null($orgId) || !Organization::inst()->isExist($orgId)) {
                throw new Exception('param organization id or organization not found.', Response::HTTP_BAD_REQUEST);
            }

            $data = $this->model->updateExec($request, $id);

            if (isset($data->errors) || isset($data->errorInfo)) {
                throw AppException::inst("update $this->name is failed.", Response::HTTP_BAD_REQUEST, $data);
            }

            return $this->json(Response::HTTP_CREATED, "update $this->name is successfully.", $data);
        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    protected function destroy($id, $orgId = null)
    {
        try {
            if (is_null($orgId) || !Organization::inst()->isExist($orgId)) {
                throw new Exception('param organization id or organization not found.', Response::HTTP_BAD_REQUEST);
            }

            $data = $this->model->destroyExec($orgId, $id);

            if (isset($data->error)) {
                throw AppException::inst("delete $this->name is failed.", Response::HTTP_BAD_REQUEST, $data);
            }

            return $this->json(Response::HTTP_OK, "delete $this->name is successfully.", $data);
        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    //TODO (jee) : blum ada validasi jika data memang tidak ditemukan
    public function setPrimary($orgId, $id)
    {
        $data = $this->model->getByOrgRef($orgId)->get();
        if ($data->count() > 0) {
            $result = $data->each(function ($x) use ($id) {
                $x->id == $id ? $x->is_primary = true : $x->is_primary = false;
                $x->save();
                return $x;
            });

            if ($result->has('errors')) {
                throw AppException::inst("set primary contact is failed.", $result);
            }

            return $this->json(Response::HTTP_OK, "set primary contact successfully", $data);
        }
        throw AppException::inst("set primary contact is failed, data not found.");
    }

    public function verification()
    {
        try {
            $arrReq = $this->request->input();

            $validate = Validator::make($arrReq, [
                'org' => 'required|integer|exists:organization,id',
                'token' => 'required|string'
            ]);

            if ($validate->fails) {
                throw AppException::inst('Bad Request', Response::HTTP_BAD_REQUEST, $validate->errors);
            }

            $callback = $this->model->verifyTokenExec($arrReq);

            if (!$callback) {
                throw AppException::inst('Bad Request', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->json(Response::HTTP_OK, 'verification email successfully', ['status' => $callback]);


        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }

    }

    public function resendVerification($orgId, $id)
    {
        try {
            $data = $this->model->getByOrgAndIdRef($orgId, $id)->first();
            if (!$data) {
                throw AppException::inst("data not found", $data);
            }

            Mail::to($data->email)
                ->send(
                    new VerificationContact(
                        'Verification Contact',
                        htmlspecialchars(url("/verify_contact?org=") . $orgId .
                            '&token=' . $data->verification_token, ENT_NOQUOTES),
                        Auth::User()
                    ));

            return $this->json(Response::HTTP_OK, "resend verification email sent.", $data);
        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }
}
