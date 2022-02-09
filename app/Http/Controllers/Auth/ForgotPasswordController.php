<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AppException;
use App\Http\Controllers\Base\BaseController;
use App\Models\Application;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Class ForgotPasswordController
 * @package App\Http\Controllers\Auth
 */
class ForgotPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function getResetToken(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email',
                'application' => 'required|string|in:inventory,invoice'
            ]);

            if ($validate->fails()) {
                $errData = $validate->messages()->first();
                throw AppException::inst(
                    $errData,
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $validate->messages()->first());
            }

            $user = User::where('email', $request->input('email'))->first();

            $app = Application::findByName($request->input('application'))->first();

            if (!$user || !$user->hasApplication($app->id)) {
                throw AppException::flash(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    trans('passwords.user') . ' or ' . trans('users.application'));
            }

            $token = $this->broker()->createToken($user);

            $user->sendPasswordResetNotification($token, $app->name);

            return $this->json(
                Response::HTTP_OK,
                trans('messages.reset_password_instruction'),
                ['status' => true]
            );
        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('errors.404');
    }
}
