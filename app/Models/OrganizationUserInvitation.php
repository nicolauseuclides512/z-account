<?php

namespace App\Models;

use App\Exceptions\AppException;
use App\Jobs\VerificationUserInvitationMailJob;
use App\Utils\StringUtil;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class OrganizationUserInvitation
 * @package App\Models
 */
class OrganizationUserInvitation extends MasterModel
{
    /**
     * @var string
     */
    protected $table = 'organization_user_invitations';

    /**
     * @var array
     */
    protected $fillable = [
        'token',
        'expired_token',
        'user_id',
        'organization_id',
        'role_id',
        'is_reset_password',
        'status',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'token',
        'expired_token',
        'is_reset_password',
        "created_at",
        "created_by",
        "updated_at",
        "updated_by",
        "deleted_at",
        "deleted_by"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return MasterModel|OrganizationUserInvitation
     */
    public static function inst()
    {
        return new self();
    }

    /**
     * fetch your organization data
     * @return mixed
     */
    public function scopeFetchByOrganization($q)
    {
        $data = $q
            ->where('organization_id', Auth::User()->getCurrentOrganization()->id)
            ->with([
                'user' => function ($q) {
                    return $q->select('id', 'username', 'email');
                },
                'organization' => function ($q) {
                    return $q->select('id', 'name', 'logo', 'portal');
                },
                'role' => function ($q) {
                    return $q->select('id', 'name');
                }])
            ->get()
            ->makeHidden([
                'id',
                'user_id',
                'role_id',
                'organization_id',
                'user'
            ]);

        return $data->map(function ($o) {
            $role = $o->role->name;
            unset($o->role);

            $o->organization_name = $o->organization->name;
            $o->organization_logo = $o->organization->logo;
            $o->organization_portal = $o->organization->portal;
            $o->username = $o->user->username;
            $o->email = $o->user->email;
            $o->role = $role;

            unset($o->organization);
            unset($o->user);

            return $o;
        });


    }

    /**
     * check existing invitation record
     *
     * @param $userId
     * @param $organizationId
     * @return bool
     */
    private function _isInvitationExist($userId, $organizationId)
    {
        return $this->where('user_id', $userId)
                ->where('organization_id', $organizationId)
                ->count() > 0;
    }

    /**
     * send invitation email
     *
     * @param $token
     * @param $organization
     * @param $user
     */
    private function _sendInvitationEmail($token, $organization, $user)
    {
        if (is_int($organization))
            $organization = Organization::find($organization);

        $myMail = (new VerificationUserInvitationMailJob(
            'Zuragan Invitation',
            $organization->name,
            htmlspecialchars(
                url("/invites/accept") . '/' . $token,
                ENT_NOQUOTES),
            $user))
            ->delay(Carbon::now()->addSecond(5));

        dispatch($myMail);
    }

    /**
     * It's for invite user to your organization
     *
     * Request Param [email, role, password (opt)]
     * @param Request $request
     * @return OrganizationUserInvitation
     * @throws Exception
     */
    public function inviteUserToOrganization(Request $request)
    {
        DB::beginTransaction();

        try {

            $org = Auth::User()->getCurrentOrganization();
            //create user and organization
            $existingUser = User::inst()
                ->getByEmail($request->get('email'));

            if ($existingUser) {

                if ($this->_isInvitationExist($existingUser->id, $org->id)) {
                    throw AppException::flash(
                        Response::HTTP_CONFLICT,
                        "The user has been invited.");
                }

                //create invite is_reset_password = false
                $inviteData = [
                    'user_id' => $existingUser->id,
                    'is_reset_password' => false,
                ];
            } else {
                //create new user + organization
                $existingUser = User::inst()->registerUser([
                    'application_id' => Auth::User()
                        ->getCurrentOrganization()->application_id,
                    'email' => $request->get('email'),
                    'password' => empty($request->get('password'))
                        ? StringUtil::uniqrandom(10)
                        : $request->get('password'),
                    'organization_name' => StringUtil::uniqrandom(10)
                ], false);

                $inviteData = [
                    'user_id' => $existingUser->id,
                    'is_reset_password' =>
                        empty($request->get('password')) ? true : false,
                ];
            }

            $inviteData = array_merge($inviteData, [
                'organization_id' => Auth::User()->getCurrentOrganization()->id,
                'token' => md5(StringUtil::uniqrandom(10)),
                'expired_token' => Carbon::now()->addDay(1),
                'role_id' => Role::inst()->getByName($request->get('role'))->id,
            ]);

            //create invitation
            $createdInvitation = OrganizationUserInvitation::create($inviteData);

            DB::commit();

            //send email
            $this->_sendInvitationEmail(
                $createdInvitation->token,
                $createdInvitation->organization_id,
                $existingUser
            );

            return $createdInvitation;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('invite user failed message: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * verify invitation using token param
     * return boolean true/false to ensure user need reset password or not
     *
     * @param $token
     * @return OrganizationUserInvitation
     * @throws Exception
     */
    public function verifyInvitation($token): OrganizationUserInvitation
    {
        try {
            $existedInvitation = $this
                ->where('token', $token)
                ->with([
                    'user' => function ($q) {
                        return $q->select('id', 'username', 'email');
                    },
                    'organization' => function ($q) {
                        return $q->select('id', 'name', 'application_id');
                    },
                    'role' => function ($q) {
                        return $q->select('id', 'name');
                    }])
                ->first();

            if (!$existedInvitation) {
                throw AppException::flash(
                    Response::HTTP_NOT_FOUND,
                    "Your invitation not found.");
            }

            if ($existedInvitation->status) {
                throw AppException::flash(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    "Your account is accepted.");
            }

            //if invitation expired
            if ($existedInvitation->expired_token < Carbon::now()) {
                //regenerate token and expired token date
                $existedInvitation->token = md5(StringUtil::uniqrandom(10));
                $existedInvitation->expired_token = Carbon::now()->addDay(1);
                if (!$existedInvitation->save()) {
                    throw AppException::flash(
                        Response::HTTP_INTERNAL_SERVER_ERROR,
                        'Create new token failed');
                }

                //send email
                $this->_sendInvitationEmail(
                    $existedInvitation->token,
                    $existedInvitation->organization,
                    $existedInvitation->user
                );

                Log::info('resend invitation email.');

                throw AppException::flash(
                    Response::HTTP_BAD_REQUEST,
                    'your invitation has expired. We prepare to resend token to your email.');
            }

            //set true invitation data
            $existedInvitation->status = true;
            if (!$existedInvitation->save()) {
                throw AppException::flash(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'Update invite failed');
            }

            //skip if Exist data on organization user
            if (OrganizationUser::inst()
                ->isUserInOrganizationExist(
                    $existedInvitation->user_id,
                    $existedInvitation->organization_id)) {
                throw AppException::flash(
                    Response::HTTP_CONFLICT,
                    "User is exist in the organization");
            }

            //set user status true when it false
            User::where('id', $existedInvitation->user_id)
                ->where('status', false)
                ->update(['status' => true]);

            //add to user organization
            OrganizationUser::create([
                'user_id' => $existedInvitation->user_id,
                'organization_id' => $existedInvitation->organization_id,
                'role_id' => $existedInvitation->role_id,
                'status' => OrganizationUser::STATUS['ACTIVE'],
            ]);

            return $this
                ->where('id', $existedInvitation->id)
                ->first();

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * resend invitation email
     *
     * @param $id
     * @return OrganizationUserInvitation
     * @throws Exception
     */
    public function resendInvitationEmail($id): OrganizationUserInvitation
    {
        try {

            $existedInvitation = $this
                ->where('id', $id)
                ->with(['user', 'organization'])
                ->first();

            if (!$existedInvitation) {
                throw AppException::flash(
                    Response::HTTP_BAD_REQUEST,
                    'Invitation user, not found.');
            }

            if ($existedInvitation->status) {
                throw AppException::flash(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    'The user invitation has been verified');
            }

            $existedInvitation->token = md5(StringUtil::uniqrandom(10));
            $existedInvitation->expired_token = Carbon::now()->addDay(1);
            if (!$existedInvitation->save()) {
                throw AppException::flash(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'Create new token failed');
            }

            Log::info('resend invitation email.');
            $this->_sendInvitationEmail(
                $existedInvitation->token,
                $existedInvitation->organization,
                $existedInvitation->user
            );

            return $existedInvitation;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
