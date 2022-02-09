<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AppException;
use App\Http\Controllers\Base\BaseController;
use App\Models\Application;
use App\Models\OrganizationUser;
use App\Models\User;
use App\Services\Gateway\Base\BaseServiceContract;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    private $appModel;

    /**
     * Create a new controller instance.
     *
     * @internal param Request $request
     * @param Request $request
     * @param Application $appModel
     */
    public function __construct(
        Request $request,
        Application $appModel
    )
    {
        $this->middleware('guest');
        $this->appModel = $appModel;
        parent::__construct(User::inst(), $request);
    }

    /**
     * @return mixed
     */
    public function register()
    {
        try {

            $data = $this->request->all();

            $validate = $this->validator($data);

            if ($this->request->acceptsJson() || $this->request->isJson()) {

                if ($this->model->checkAvailabilityEmailInApplication($data))
                    return $this->json(
                        Response::HTTP_ACCEPTED,
                        trans('messages.not_registered'));

                if ($validate->fails()) {
                    return $this->json(
                        Response::HTTP_BAD_REQUEST,
                        $validate->getMessageBag());
                }

                event(new Registered($user = $this->create($this->request->all())));

                if ($user) {
                    return $this->json(Response::HTTP_CREATED, trans('messages.user_created'), ['status' => true]);
                }

                return $this->json(
                    Response::HTTP_BAD_REQUEST,
                    'user exist.',
                    ['status' => false]
                );
            }

            return $this->json(
                Response::HTTP_BAD_REQUEST,
                'Invalid header');
        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function registerOrganization()
    {
        try {

            $data = $this->request->input();
            $data['email'] = strtolower($data['email']);

            $validate = Validator::make($data, [
                'organization_name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'application' => 'required|string|in:invoice,inventory',
                'password' => 'required|min:8',
                'phone' => 'nullable|string|min:9|max:255',
            ]);

            if ($validate->fails()) {
                return $this->json(
                    Response::HTTP_BAD_REQUEST,
                    $validate->getMessageBag()
                );
            }

            $user = $this->model->registerOrganization($data);

            if ($user) {
                return $this->json(
                    Response::HTTP_CREATED,
                    'organization created.',
                    ['status' => true]
                );
            }

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }

    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $data['email'] = strtolower($data['email']);
        return Validator::make($data, [
            'organization_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8',
            'application' => 'required|string|in:invoice,inventory',
            'phone' => 'nullable|string|min:9|max:255',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     * @throws \Exception
     */
    protected function create(array $data = null)
    {
        try {

            //TODO(jee): change this to func in Application
            $this->fillApplicationId($data);

            return $this->model->registerUser($data);

        } catch (AppException $e) {
            throw $e;
        }
    }

    /**
     * @param $data
     * @throws AppException
     */
    private function fillApplicationId(&$data)
    {
        if (isset($data['application'])) {
            $app = $this->appModel->findByName($data['application'])->first();
            if (!$app) {
                throw AppException::inst(
                    'Invalid application specified',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $data['application_id'] = $app->id;
        } else {
            $app = $this->appModel->orderBy('id', 'asc')->first();
            if (!$app) {
                throw AppException::inst(
                    'Application is not setup yet',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
            $data['application_id'] = $app->id;
        }
    }

    public function resendVerificationEmail()
    {
        try {

            $email = strtolower($this->request->input('email'));

            $validate = Validator::make(['email' => $email], [
                'email' => 'required|email|max:255'
            ]);

            if ($validate->fails()) {
                throw AppException::inst(
                    'Invalid input param',
                    Response::HTTP_BAD_REQUEST,
                    $validate->getMessageBag());
            }

            $this->model->resendVerificationEmailExec($email);

            return $this->json(
                Response::HTTP_CREATED,
                trans('messages.cek_verification_email_desc'),
                ['status' => true]
            );
        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * verifikasi user setelah registrasi
     * @return mixed
     * @internal param $ $verificationCode* $verificationCode
     * @internal param $activationCode
     */
    public function verifyUser()
    {

        $verificationCode = $this->request->get('uid');

        $user = $this->model->verifyUserExec($verificationCode);

        $url = $user->app_identity === 2
            ? env('INVENTORY_WEB_URL')
            : env('INVOICE_WEB_URL');

        try {

            if (!$user) {
                throw AppException::inst(
                    'Activate Your Email Failed.',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return redirect($url . '?email=' . $user->email . '&activation_status=true&message=Your Email Is Activated.')
                ->with(['message', 'Your Email Is Activated.', 'loginUrl' => $url]);

        } catch (Exception $e) {
            return redirect($url . '?activation_status=false&message=' . $e->getMessage())
                ->with(['message', 'Your Is Not Activated.', 'loginUrl' => $url]);
        }
    }
}
