<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AppException;
use App\Http\Controllers\Base\BaseController;
use App\Models\Application;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request, $token = null)
    {
        if ($token) {
            //note: character has been removed by request
            $email = str_replace(' ', '+', $request->email);
            return view('auth.passwords.reset')->with(
                ['token' => $token, 'email' => $email]
            );
        }
        return redirect($this->redirectTo);
    }

    public function reset(Request $request)
    {
        try {
            if (empty($request->input())) {
                return view('errors.400')->with(['message' => 'Something gone wrong.']);
            }

            $this->validate($request, $this->rules(), $this->validationErrorMessages());

            $app = Application::findByName($request->input('aid'))->firstOrFail();

            // Here we will attempt to reset the user's password. If it is successful we
            // will update the password on an actual user model and persist it to the
            // database. Otherwise we will parse the error and return the response.
            $response = $this->broker()->reset(
                $this->credentials($request),
                function ($user, $password) use ($app) {
                    if ($user->hasApplication($app->id))
                        $this->resetPassword($user, $password);
                    else
                        throw AppException::inst(
                            "Application Not Found",
                            Response::HTTP_BAD_REQUEST);
                });

            if ($response != Password::PASSWORD_RESET) {
                return view('errors.400')->with(['message' => trans($response) ?? 'Something gone wrong.']);
            }

            switch (strtolower($app->name)) {
                case "inventory" :
                    $url = env('INVENTORY_WEB_URL');
                    break;
                case "invoice" :
                    $url = env('INVOICE_WEB_URL');
                    break;
                default :
                    throw AppException::flash(
                        Response::HTTP_NOT_FOUND,
                        "Application request does't exist."
                    );
            }

            return redirect()
                ->away("$url?msg=" . trans('passwords.reset'));

//                ->with([
//                    'message' => trans('passwords.reset'),
//                    'loginUrl' => $url]);

        } catch (\Exception $e) {
            return view('errors.500')
                ->with(['message' => $e->getMessage()]);
        }
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'aid' => 'required|in:invoice,inventory'
        ];
    }

    public function redirectPath()
    {
        return $this->redirectTo;
    }

    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => Str::random(60),
        ])->save();
    }

}
