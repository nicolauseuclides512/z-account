<?php

namespace App\Http\Controllers;

use App\Cores\Image;
use App\Cores\TokenGenerator;
use App\Exceptions\AppException;
use App\Http\Controllers\Base\BaseController;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Services\Gateway\Rest\RestService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;

/**
 * Class OrganizationController
 * @package App\Http\Controllers
 */
class OrganizationController extends BaseController
{
    /**
     * @var string
     */
    protected $layout = 'organizations';

    /**
     * @var string
     */
    public $name = 'Organization Profile';

    /**
     * @var string
     */
    protected $redirectTo = '/admin/organizations';

    /**
     * @var array
     */
    public $sortBy = ['id', 'name', 'created_at', 'updated_at'];

    /**
     * @var RestService
     */
    private $cli;

    /**
     * OrganizationController constructor.
     * @param Request $request
     * @param null $orgId
     */
    public function __construct(Request $request, $orgId = null)
    {
        parent::__construct(Organization::inst(), $request);

        $this->cli = new RestService(
            new Client([
                'timeout' => Config::get('gateway.timeout'),
                'connect_timeout' =>
                    Config::get('gateway.timeout',
                        Config::get('gateway.timeout')
                    )
            ])
        );

        $this->cli->setBaseUri(env('GATEWAY_ASSET_SERVICE'));

    }

    /**
     * @return array|\Illuminate\Http\JsonResponse|null
     */
    public function _resource()
    {
        $existingOrg = Auth::User()->getCurrentOrganization();

        try {
            $this->cli->setHeaders([
                'headers' => [
                    'Authorization' =>
                        'Bearer ' . TokenGenerator::inst()
                            ->create(
                                $existingOrg->id,
                                $this->request->bearerToken()
                            ),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $promise = [
                'countries' => $this->cli->getAsync('/countries/list'),
                'provinces' => $this->cli->getAsync('/provinces/list'),
                'timezones' => $this->cli->getAsync('/timezones/list'),
                'currencies' => $this->cli->getAsync('/currencies/list')
            ];

            $res = Promise\unwrap($promise);

            return [
                'countries' => json_decode($res['countries']->getBody())->data ?? [],
                'provinces' => json_decode($res['provinces']->getBody())->data ?? [],
                'timezones' => json_decode($res['timezones']->getBody())->data ?? [],
                'currencies' => json_decode($res['currencies']->getBody())->data ?? []
            ];

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        } catch (\Throwable $e) {
            return $this->jsonExceptions($e);
        }

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    protected function index()
    {
        if ($this->request->expectsJson()) {
            if ($this->request->get('type') == 'dataTable')
                return Datatables::collection($this->model->get())->make(true);

            $data = $this->model
                ->getInUser()
                ->filter($this->requestMod()['filter_by'], $this->requestMod()['q'])
                ->orderBy(
                    $this->requestMod()['sort_column'],
                    $this->requestMod()['sort_order'])
                ->paginate($this->request->input("per_page"));

            return $this->json(Response::HTTP_OK, 'success', $data);
        }

        return view("$this->rootLayout.$this->layout.index");
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function getByUserId($id)
    {
        if ($this->request->expectsJson()) {
            if ($this->request->get('type') == 'dataTable') {
                $data = $this->model->whereHas('users', function ($q) use ($id) {
                    return $q->where('user_id', $id);
                })->get();

                return Datatables::collection($data)->make(true);
            }
        }
    }

    /**
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|null
     */
    protected function store()
    {
        try {
            $request = $this->request->input();

            $data = $this->model->storeExec($request);

            if (isset($data->errors) || isset($data->errorInfo)) {
                throw AppException::inst("Save $this->name is failed.", Response::HTTP_BAD_REQUEST, $data);
            }

            if ($this->request->expectsJson()) {
                return $this->json(Response::HTTP_CREATED, "Save $this->name is successfully.", $data);
            }

            if ($this->request->file('logo')) {

                $uploaded = $this->request->file('logo')
                    ->store(Image::generatePath($data->id, 'organization'), 's3');

                if (!$uploaded)
                    throw AppException::inst('upload is failed.', Response::HTTP_BAD_REQUEST);

                $data->logo = env('S3_URL') . "/$uploaded";

                if (!$data->save()) {
                    return redirect($this->redirectTo . '/create')->withErrors($data->errors);
                }
            }

            return redirect($this->redirectTo);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * @param $id
     * @return $this|\Illuminate\Http\JsonResponse|null
     */
    protected function show($id)
    {
        try {

            $data = $this->model->getByIdRef($id)->firstOrFail();

            if ($this->request->expectsJson()) {
                return $this->json(Response::HTTP_OK, trans('messages.company_profile_fetched'), $data);
            }

            $this->cli->setHeaders([
                'headers' => [
                    'Authorization' => 'Bearer ' . TokenGenerator::inst()->createToken(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $countryId = $data->country_id ?? 'undefined';
            $provinceId = $data->province_id ?? 'undefined';
            $districtId = $data->district_id ?? 'undefined';
            $regionId = $data->district_id ?? 'undefined';
            $timezoneId = $data->timezone_id ?? 'undefined';
            $currencyId = $data->currency_id ?? 'undefined';

            $promise = [
                'country' => $this->cli->getAsync('/countries/' . $countryId),
                'province' => $this->cli->getAsync('/provinces/' . $provinceId),
                'district' => $this->cli->getAsync('/districts/' . $districtId),
                'region' => $this->cli->getAsync('/regions/' . $regionId),
                'timezone' => $this->cli->getAsync('/timezones/' . $timezoneId),
                'currency' => $this->cli->getAsync('/currencies/' . $currencyId),
            ];

            $res = Promise\unwrap($promise);

            $data->country = json_decode($res['country']->getBody())->data ?? [];
            $data->province = json_decode($res['province']->getBody())->data ?? [];
            $data->district = json_decode($res['district']->getBody())->data ?? [];
            $data->region = json_decode($res['region']->getBody())->data ?? [];
            $data->timezone = json_decode($res['timezone']->getBody())->data ?? [];
            $data->currency = json_decode($res['currency']->getBody())->data ?? [];

            return view("$this->rootLayout.$this->layout.detail")->with('data', $data);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        } catch (\Throwable $th) {
            return $this->jsonExceptions($th);
        }
    }

    /**
     * @param $id
     * @return $this|\Illuminate\Http\JsonResponse|null
     */
    protected function edit($id)
    {
        try {
            $data = $this->_resource();

            $data['data'] = $this->model->getByIdRef($id)->firstOrFail();

            if ($this->request->expectsJson()) {
                return $this->json(Response::HTTP_OK, 'success', $data);
            }

            $this->cli->setHeaders([
                'headers' => [
                    'Authorization' => 'Bearer ' . TokenGenerator::inst()->createToken(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $districtId = $data['data']->district_id ?? 'undefined';
            $regionId = $data['data']->district_id ?? 'undefined';

            $promise = [
                'district' => $this->cli->getAsync('/districts/' . $districtId),
                'region' => $this->cli->getAsync('/regions/' . $regionId),
            ];

            $res = Promise\unwrap($promise);

            $data['data']->district = json_decode($res['district']->getBody())->data ?? [];
            $data['data']->region = json_decode($res['region']->getBody())->data ?? [];

            return view("$this->rootLayout.$this->layout.create")->with($data);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        } catch (\Throwable $th) {
            return $this->jsonExceptions($th);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse|null
     */
    protected function getDistrictByProvinceId($id)
    {
        try {
            $this->cli->setHeaders([
                'headers' => [
                    'Authorization' => 'Bearer ' . TokenGenerator::inst()->createToken(),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $promise = [
                'districts' => $this->cli->getAsync('/districts/provinces/' . $id),
            ];

            $res = Promise\unwrap($promise);

            return $this->json(Response::HTTP_OK, 'get district by province', json_decode($res['districts']->getBody())->data ?? []);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        } catch (\Throwable $th) {
            return $this->jsonExceptions($th);
        }
    }

    /**
     * @param $orgId
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function uploadLogo($orgId)
    {
        try {
            if ($this->request->file('logo')) {
                $allRequest = $this->request->all();

                array_walk($allRequest, function ($item, $key) {
                    \Log::info("$key => $item");
                });
                $validator = Validator::make($allRequest, [
                    'logo' => 'required|mimes:jpeg,png',
                ]);

                if ($validator->fails()) {
                    throw AppException::inst(
                        "Invalid image uploaded",
                        Response::HTTP_UNPROCESSABLE_ENTITY,
                        $validator->errors()
                    );
                }

                if (is_null($orgId))
                    throw AppException::inst(
                        'organization key not found.',
                        Response::HTTP_BAD_REQUEST
                    );

                $org = $this
                    ->model
                    ->getByidRef($orgId)
                    ->firstOrFail();

                if ($org->logo) {
                    $logo = str_replace(env('S3_URL'), '', $org->logo);
                    Image::removeObject($logo);
                }

                $uploaded = $this
                    ->request
                    ->file('logo')
                    ->store(
                        Image::generatePath($orgId, 'organization'),
                        's3'
                    );

                if (!$uploaded)
                    throw AppException::inst(
                        'upload is failed.',
                        Response::HTTP_BAD_REQUEST
                    );

                $org->logo = env('S3_URL') . "/$uploaded";

                if (!$org->save())
                    throw AppException::inst('image logo failed to update.',
                        Response::HTTP_INTERNAL_SERVER_ERROR, $org->errors);

                return $this->json(Response::HTTP_OK, 'Logo updated',
                    array('logo' => $org->logo));
            }

            throw AppException::inst("HTTP_BAD_REQUEST", Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * @param $orgId
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function removeLogo($orgId)
    {
        try {

            if (is_null($orgId)) {
                throw AppException::inst(
                    'organization key not found.',
                    Response::HTTP_BAD_REQUEST);
            }

            $org = $this->model->getByidRef($orgId)->firstOrFail();
            $logo = str_replace(env('S3_URL'), '', $org->logo);

            //TODO (jee) : disini harusnya ada validasi sukses atau gagal
            Image::removeObject($logo);
            $org->logo = null;

            if (!$org->save()) {
                throw AppException::inst(trans('messages.failed_remove_logo'), Response::HTTP_INTERNAL_SERVER_ERROR, $org->errors);
            }

            return $this->json(Response::HTTP_OK, trans('messages.logo_removed'), $org->logo);

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function getMyOrganizations()
    {
        $relatedOrg = Auth::User()
            ->getRelatedOrganizations();

        return $this->json(
            Response::HTTP_OK,
            "fetch my organization successfully.",
            $relatedOrg);
    }

    public function getUsersInMyOrganization()
    {
        $data = Auth::User()
            ->getCurrentOrganization()
            ->getUsersInOrganizationRef()
            ->get()
            ->makeHidden([
                'is_primary',
                'organization_id'
            ]);

        $data
            ->map(function ($o) {
                $o->username = $o->user->username;
                $o->email = $o->user->email;
                $role = $o->role->name;
                unset($o->role);
                unset($o->user);
                $o->role = $role;
                return $o;
            });

//        $data['status'] = OrganizationUser::STATUS;

        return $this->json(
            Response::HTTP_OK,
            "fetch my organization successfully.",
            $data
        );
    }

    public function setUserStatusInMyOrganization(int $id, int $status)
    {
        $orgUser = Auth::User()
            ->getCurrentOrganization()
            ->getUsersInOrganizationRef()
            ->where('id', $id)
            ->update(['status' => $status]);

        return $this->json(
            Response::HTTP_OK,
            "set status successfully updated.",
            $orgUser);

    }

    protected function update($id)
    {
        try {
            $request = $this->request->input();

            $data = $this->model->updateExec($request, $id);

            if (isset($data->errors) || isset($data->errorInfo)) {
                throw AppException::inst("update $this->name is failed.", Response::HTTP_BAD_REQUEST, $data);
            }

            if ($this->request->expectsJson()) {
                return $this->json(Response::HTTP_CREATED, trans('messages.organization_profile_updated'), $data);
            }

            return redirect($this->redirectTo);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function switchOrganization($id)
    {
        $organizationUser = OrganizationUser::inst()
            ->with([
                "role" => function ($q) {
                    return $q->select("id", "name")
                        ->with(['permissions' => function ($q) {
                            return $q->select('name', 'role_id');
                        }]);
                },
                "organization" => function ($q) {
                    return $q;
                }
            ])
            ->where('organization_id', $id)
            ->where('user_id', Auth::User()->id)
            ->firstOrFail();

        $role = $organizationUser->role['name'];
        $scopes = $organizationUser
            ->role
            ->permissions
            ->map(function ($q) {
                return $q->name;
            });

        unset($organizationUser->role->permissions);

        return $this->json(
            Response::HTTP_OK,
            'success', [
                "organization" => $organizationUser->organization,
                "role" => $role,
                "scopes" => $scopes
            ]
        );
    }

    protected function indexAdmin()
    {
        $data = $this->model
            ->select('id', 'name', 'phone', 'region_id', 'district_id', 'province_id', 'created_at', 'status')
            ->filter($this->requestMod()['filter_by'], $this->requestMod()['q'])
            ->orderBy(
                $this->requestMod()['sort_column'],
                $this->requestMod()['sort_order'])
            ->paginate($this->request->input("per_page"));

        return $this->json(
            Response::HTTP_OK,
            'success',
            $data
        );

    }
}
