<?php

namespace App\Http\Controllers;

use App\Cores\Asset;
use App\Cores\Image;
use App\Cores\TokenGenerator;
use App\Exceptions\AppException;
use App\Http\Controllers\Base\BaseController;
use App\Models\Application;
use App\Models\Role;
use App\Models\User;
use App\Services\Gateway\Rest\RestService;
use BadMethodCallException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;
use Carbon\Carbon;


/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends BaseController
{
    /**
     * @var string
     */
    protected $layout = 'users';

    /**
     * @var string
     */
    protected $name = 'User';

    /**
     * @var string
     */
    protected $redirectTo = '/admin/users';

    /**
     * @var array
     */
    protected $sortBy = [
        'id',
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * UserController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct(User::inst(), $request);
    }

    /**
     * @return array|\Illuminate\Http\JsonResponse|null
     */
    public function _resource()
    {
        try {

            $cli = new RestService(
                new Client([
                    'timeout' => Config::get('gateway.timeout'),
                    'connect_timeout' =>
                        Config::get('gateway.timeout',
                            Config::get('gateway.timeout')
                        )
                ])
            );
            $cli->setBaseUri(env('GATEWAY_ASSET_SERVICE'));
            $token = TokenGenerator::inst()->createToken();
            $cli->setHeaders([
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);
            $promise = [
                'countries' => $cli->getAsync('/countries/list'),
                'timezones' => $cli->getAsync('/timezones/list')
            ];
            $res = Promise\unwrap($promise);
            Log::info(json_encode($res['countries']));
            return [
                'countries' => json_decode($res['countries']->getBody())->data ?? [],
                'timezones' => json_decode($res['timezones']->getBody())->data ?? [],
                'genders' => Asset::GENDERS
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
     * @return $this|\Illuminate\Http\JsonResponse
     */
    protected function create()
    {
        if ($this->request->expectsJson()) {
            return $this->json(Response::HTTP_OK, 'success', $this->_resource());
        }

        $resource = $this->_resource();
        $resource['access_types'] = Asset::ACCESS_TYPES;

        return view("$this->rootLayout.$this->layout.create")->with($resource);
    }

    /**
     * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|null
     */
    protected function store()
    {
        try {
            //TODO (jee) : validate param request
            $request = $this->request->input();

            $validate = Validator::make($request, [
                'password' => 'required|min:6|confirmed',
            ]);

            if ($validate->fails()) {
                if ($this->request->expectsJson()) {
                    return $this->json(Response::HTTP_BAD_REQUEST, $validate->errors());
                }

                return redirect($this->request->path() . '/create')->withErrors($validate->errors());

            }

            $data = $this->model->storeExec($request);

            //TODO (jee) : PR bro ga ketangkep exception master
            if ($data instanceof BadMethodCallException) {
                throw $data;
            }

            if ($this->request->expectsJson()) {
                if (isset($data->errors) || isset($data->errorInfo)) {
                    throw AppException::inst("Save $this->name is failed.", Response::HTTP_BAD_REQUEST, $data);
                }
                return $this->json(Response::HTTP_CREATED, "Save $this->name is successfully.", $data);
            }

            if (isset($data->errors) || isset($data->errorInfo)) {
                return redirect($this->request->path())->with('errors', $data->errors);
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

            $data = $this->model->getByIdRef((int)$id)->firstOrFail();

            if ($this->request->expectsJson())
                return $this->json(Response::HTTP_OK, 'success', $data);

            $cli = new RestService(
                new Client([
                    'timeout' => Config::get('gateway.timeout'),
                    'connect_timeout' =>
                        Config::get('gateway.timeout',
                            Config::get('gateway.timeout')
                        )
                ])
            );

            $cli->setBaseUri(env('GATEWAY_ASSET_SERVICE'));

            $token = TokenGenerator::inst()->createToken();

            $cli->setHeaders([
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            $country_id = $data->country_id ?? 'undefined';
            $timezone_id = $data->timezone_id ?? 'undefined';

            $promise = [
                'country' => $cli->getAsync('/countries/' . $country_id),
                'timezone' => $cli->getAsync('/timezones/' . $timezone_id)
            ];

            $res = Promise\unwrap($promise);

            $data->country = json_decode($res['country']->getBody())->data ?? [];
            $data->timezone = json_decode($res['timezone']->getBody())->data ?? [];

            $gender = null;
            foreach (Asset::GENDERS as $k => $v) {
                if ($k === $data->gender) {
                    $gender = $v;
                }
            }

            $data->gender = $gender;

            return view("$this->rootLayout.$this->layout.detail")->with('data', $data);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        } catch (\Throwable $e) {
            return $this->jsonExceptions($e);
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
            $data['data'] = $this->model
                ->getByIdRef($id)
                ->firstOrFail();

            if ($this->request->expectsJson()) {
                return $this->json(
                    Response::HTTP_OK,
                    'success', $data);
            }

            $data['access_types'] = Asset::ACCESS_TYPES;

            return view("$this->rootLayout.$this->layout.create")
                ->with($data);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * for internal access, return datatable
     *
     * @param $id
     * @return mixed
     */
    protected function getByOrganizationId($id)
    {
        if ($this->request->expectsJson()) {
            if ($this->request->get('type') == 'dataTable') {
                $data = $this->model->whereHas('organizations', function ($q) use ($id) {
                    return $q->where('organization_id', $id);
                })->get();

                return Datatables::collection($data)->make(true);
            }
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function checkAvailabilityEmailInApp()
    {
        try {

            $data = $this->request->input();
            $data['email'] = strtolower($data['email']);
            $validate = Validator::make($data, [
                'email' => 'required|email|max:255',
                'application' => 'sometimes|string|in:invoice,inventory',
            ]);

            if ($validate->fails()) {
                throw AppException::flash(
                    Response::HTTP_BAD_REQUEST,
                    "Invalid parameters",
                    $validate->getMessageBag());
            }

            $user = $this->model->where('email', $data['email'])->first();

            if ($user) {
                $app = Application::findByName($data['application'])->firstOrFail();
                if ($user->hasApplication($app->id)) {
                    return $this->json(
                        Response::HTTP_CONFLICT,
                        'User is already existed.');
                } else {
                    return $this->json(
                        Response::HTTP_ACCEPTED,
                        "User does not registered in this application.");
                }
            }

            return $this->json(
                Response::HTTP_OK,
                "Email is available."
            );
        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function getMyProfile()
    {
        try {
            return $this->json(
                Response::HTTP_ACCEPTED,
                'User fetched.',
                $this->model
                    ->getByIdRef(Auth::id())
                    ->nested()
                    ->firstOrFail());

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function updateMyProfile()
    {
        try {
            $request = $this->request->input();
            $data = $this->model->updateExec($request, Auth::User()->id);
            return (isset($data->errors) || isset($data->errorInfo))
                ? $this->json(
                    Response::HTTP_BAD_REQUEST,
                    "$this->name update failed.",
                    $data)
                : $this->json(
                    Response::HTTP_CREATED,
                    "$this->name updated.",
                    $data);
        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function changeMyPassword()
    {
        try {
            $arrReq = $this->request->all();

            $validate = Validator::make($arrReq, [
                'old_password' => 'required|min:8',
                'password' => 'required|min:8|confirmed',
            ]);

            if ($validate->fails()) {
                return $this->json(Response::HTTP_BAD_REQUEST, $validate->getMessageBag());
            }

            if (!Hash::check($arrReq['old_password'], Auth::User()->password)) {
                throw AppException::inst(trans('messages.old_password_not_match'), Response::HTTP_BAD_REQUEST);
            }

            if (!$this->model->changePasswordExec($arrReq)) {
                throw AppException::flash(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'Change password failed.'
                );
            }

            return $this->json(
                Response::HTTP_CREATED,
                trans('messages.change_password_successfully'));

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function uploadMyPhoto()
    {
        try {
            if ($this->request->file('photo')) {

                $loginUser = Auth::User();

                if ($loginUser->photo) {
                    $photo = str_replace(env('S3_URL'), '', $loginUser->photo);

                    if (!Image::removeObject($photo))
                        return $this->json(Response::HTTP_BAD_REQUEST, 'remove object is failed.');
                }

                $uploaded = $this->request->file('photo')->store(Image::generatePath($loginUser->id, 'profile'), 's3');

                if (!$uploaded)
                    return $this->json(Response::HTTP_BAD_REQUEST, 'upload is failed.');

                $loginUser->photo = env('S3_URL') . "/$uploaded";

                if (!$loginUser->save())
                    return $this->json(
                        Response::HTTP_BAD_REQUEST,
                        'photo failed to update.',
                        $loginUser->errors);

                return $this->json(
                    Response::HTTP_OK,
                    'photo has been updated.',
                    ['photo' => $loginUser->photo]);

            }

            return $this->json(
                Response::HTTP_BAD_REQUEST,
                "HTTP_BAD_REQUEST");
        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function removeMyPhoto()
    {
        try {

            $loginUser = Auth::User();

            $photo = str_replace(env('S3_URL'), '', $loginUser->photo);

            //TODO (jee) : disini harusnya ada validasi sukses atau gagal
            if (Image::removeObject($photo)) {
                $loginUser->photo = null;
                if ($loginUser->save()) {
                    return $this->json(Response::HTTP_OK,
                        'photo has been delete.',
                        $loginUser->photo);
                }
            }

            return $this->json(Response::HTTP_BAD_REQUEST,
                'photo failed to delete / image does not exist.',
                $loginUser);

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    //@deprecated
    //todo(jee) the function is moved to organization controller
    public function getMyOrganizations()
    {
        $relatedOrg = Auth::User()->getRelatedOrganizations();

        return $this->json(
            Response::HTTP_OK,
            "fetch my organization successfully.",
            $relatedOrg);
    }


    protected function indexAdmin()
    {
        $data = $this->model
            ->select('id', 'username', 'email', 'access_type', 'photo', 'status', 'created_at')
            ->with([
                'organizationUser' => function ($q) {
                    return $q->select('user_id', 'organization_id', 'role_id')
                        ->with(['organization' => function ($qu) {
                            return $qu->select(['id', 'name', 'application_id', 'portal', 'phone']);
                        }]);
                },
                'providerUser' => function ($pu) {
                    return $pu->select('user_id', 'provider_id', 'provider');
                },
                'oauthAccessToken' => function ($value) {
                    return $value->select('user_id', 'created_at')
                        ->orderBy('created_at', 'desc');
                }
            ])
            ->filter($this->requestMod()['filter_by'], $this->requestMod()['q'])
            ->orderBy(
                $this->requestMod()['sort_column'],
                $this->requestMod()['sort_order'])
            ->paginate($this->request->input("per_page"));

        $data->each(function ($o) {
            $o->provider = collect($o->providerUser)
                ->map(function ($o) {
                    return $o->provider;
                });

            $lastSeen = collect($o->oauthAccessToken)->first();

            $o->last_seen = !empty($lastSeen) ? (string)$lastSeen->created_at : "";

            $o->organizations = collect($o->organizationUser)
                ->map(
                    function ($orgUser) {
                        return [
                            'role' => Role::find($orgUser->role_id)->name,
                            'id' => $orgUser->organization->id,
                            'name' => $orgUser->organization->name,
                            'portal' => $orgUser->organization->portal,
                            'phone' => $orgUser->organization->phone,
                            'application' => Application::findByIdOrName($orgUser->organization->application_id)->name,
                        ];
                    });
            unset($o->providerUser);
            unset($o->organizationUser);
            unset($o->oauthAccessToken);
            unset($o->oauth);
            return $o;
        });


        return $this->json(
            Response::HTTP_OK,
            'success',
            $data
        );
    }
}
