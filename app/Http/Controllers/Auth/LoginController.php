<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Base\BaseController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Lcobucci\JWT\Parser;


class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/home';

    /**
     * Create a new controller instance.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct(null, $request);

        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Handle a login request to the application.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function login()
    {
        $this->validateLogin($this->request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($this->request)) {

            $this->fireLockoutEvent($this->request);

            return $this->sendLockoutResponse($this->request);
        }

        if ($this->guard()
            ->attempt([
                'email' => strtolower($this->request->input('email')),
                'password' => $this->request->input('password'),
                'status' => true,
                'access_type' => 'ADMINISTRATOR'
            ], $this->request->has('remember'))
        ) {
            return $this->sendLoginResponse($this->request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($this->request);

        return $this->sendFailedLoginResponse($this->request);
    }

    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            $value = $request->bearerToken();

            $id = (new Parser())->parse($value)->getHeader('jti');

            $token = DB::table('oauth_access_tokens')
                ->where('id', '=', $id)
                ->update(['revoked' => true]);

            if ($token != 1) {
                return $this->json(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'Logout request failed.');

            }

        }

        $this->guard()->logout();

        if ($request->hasSession()) {
            $request->session()->flush();

            $request->session()->regenerate();
        }

        if ($request->expectsJson()) {

            $json = [
                'success' => true,
                'code' => 200,
                'message' => trans('messages.logout_succeed'),
            ];

            return $this->json(Response::HTTP_OK, trans('messages.logout_succeed'), $json);
        }

        return redirect('/');

    }
}
