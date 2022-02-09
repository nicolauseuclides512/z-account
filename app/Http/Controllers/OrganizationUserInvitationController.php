<?php

namespace App\Http\Controllers;

use App\Exceptions\AppException;
use App\Http\Controllers\Base\BaseController;
use App\Http\Requests\InviteUserRequest;
use App\Models\Application;
use App\Models\OrganizationUserInvitation;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Class OrganizationUserInvitationController
 * @package App\Http\Controllers
 */
class OrganizationUserInvitationController extends BaseController
{
    use SendsPasswordResetEmails;

    public $name = 'Organization User Invitation';

    public $sortBy = ['id', 'created_at', 'updated_at'];

    /**
     * OrganizationUserInvitationController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct(OrganizationUserInvitation::inst(), $request);
    }

    /**
     * show invited user in current organization
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchInvitedUserInOrganization()
    {
        return $this->json(
            Response::HTTP_OK,
            OrganizationUserInvitation::inst()->fetchByOrganization()
        );
    }

    /**
     * It's for invite user to your organization
     *
     * Request Param [email, role, password (opt)]
     * @param InviteUserRequest $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function inviteUserToOrganization(InviteUserRequest $request)
    {
        try {
            if (!OrganizationUserInvitation::inst()
                ->inviteUserToOrganization($request))
                throw AppException::flash(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    "Invite user failed.");

            return $this->json(
                Response::HTTP_CREATED,
                "Invite user successfully created.");

        } catch (Exception $e) {
            Log::error('invite user failed message: ' . $e->getMessage());
            return $this->jsonExceptions($e);
        }
    }

    /**
     * @param $token
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|null
     * @throws Exception
     */
    public function verifyInvitation($token)
    {
        try {

            $existedInvitation = OrganizationUserInvitation::inst()
                ->verifyInvitation($token);

            $app = Application::find(
                $existedInvitation
                    ->organization
                    ->application_id
            );

            if ($existedInvitation->is_reset_password) {
                Log::info('verify to reset password');
                $resetToken = $this
                    ->broker()
                    ->createToken(
                        User::find($existedInvitation->user_id));

                return redirect('/password/reset/'
                    . $resetToken
                    . '?aid=' . strtolower($app->name)
                    . "&email={$existedInvitation->user->email}")
                    ->with(['email' => $existedInvitation->user->email]);
            }

            Log::info('verify no reset password to ' . $app->name);

            return strtolower($app->name) === 'inventory'
                ? redirect()->away(env('INVENTORY_WEB_URL'))
                : redirect()->away(env('INVOICE_WEB_URL'));

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * resend invitation email
     * @param $id
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function resendInvitationEmail($id)
    {
        try {

            //resend invitation email | void func
            OrganizationUserInvitation::inst()
                ->resendInvitationEmail($id);

            return $this->json(
                Response::HTTP_CREATED,
                "Email invitation successfully sent.");

        } catch (Exception $e) {
            Log::error('invite user failed message: ' . $e->getMessage());
            return $this->jsonExceptions($e);
        }
    }

}
