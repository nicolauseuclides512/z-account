<?php

namespace App\Models;

use App\Exceptions\AppException;
use App\Jobs\ResetPasswordMailJob;
use App\Jobs\SuccessVerificationUserMailJob;
use App\Jobs\VerificationUserMailJob;
use App\Models\Base\BaseModel;
use App\Models\Traits\HasOrganizationAndRole;
use App\Utils\DateTimeUtil;
use Carbon\Carbon;
use Exception;
use function foo\func;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;

class User extends MasterModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    protected $table = 'users';

    use Authenticatable, Authorizable, CanResetPassword;

    use HasApiTokens, Notifiable, HasOrganizationAndRole;

    protected $fillable = [
        'username',
        'email',
        'access_type',
        'password',
        'full_name',
        'nickname',
        'gender',
        'status',
        'country_id',
        'timezone_id',
        'photo',
        'verification_code',
        'app_identity'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'token',
        'invitation_code',
        'reset_code',
    ];

    protected $filterNameCfg = 'users';

    protected $appends = ['primary_organization'];

    private $currentOrg = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->nestedBelongConfigs = [];
    }

    /**
     * @param string $token
     * @param string $app
     */
    public function sendPasswordResetNotification($token, $app = "invoice")
    {
        $myMail = (new ResetPasswordMailJob($app, $this->email, $token))
            ->delay(Carbon::now()->addSecond(5));

        dispatch($myMail);
    }

    public function oauthAccessToken()
    {
        return $this->hasMany(
            OauthAccessToken::class,
            'user_id'
        );
    }

    public function providerUser()
    {
        return $this->hasMany(
            SocialProviderUser::class,
            'user_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organizationUser()
    {
        return $this->hasMany(
            OrganizationUser::class,
            'user_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organizations()
    {
        return $this->belongsToMany(
            Organization::class, 'organization_users',
            'organization_id'
        );
    }

    /**
     * @param null $id
     * @return array
     */
    public static function rules($id = null)
    {
        return [
            'username' => 'nullable|string',
            'email' => 'required|email',
            'access_type' => 'required|string|in:ADMINISTRATOR,USER',
            'password' => 'required|string',
            'full_name' => 'nullable|string',
            'nickname' => 'nullable|string',
            'gender' => 'nullable|integer|in:0,1,2,9',
            'status' => 'boolean',
            'country_id' => 'nullable|integer',
            'timezone_id' => 'nullable|integer',
            'photo' => 'nullable|string',
            'verification_code' => 'nullable|string',
            'app_identity' => 'required|integer|exists:applications,id',
        ];
    }

    /**
     * @return MasterModel|User
     */
    public static function inst()
    {
        return new User();
    }

    /**
     * @param $request
     * @param BaseModel|null $model
     * @return BaseModel|MasterModel|User
     */
    public function populate($request, BaseModel $model = null): User
    {
        $req = new Collection($request);
        if (is_null($model)) {
            $model = self::inst();
        }

        if ($req->get('action') == 'store')
            $model->password = bcrypt($req->get('password'));

        $model->username = $req->get('username');
        $model->email = $req->get('email');
        $model->access_type = $req->get('access_type') ?? 'USER';
        $model->full_name = $req->get('full_name');
        $model->nickname = $req->get('nickname');
        $model->gender = $req->get('gender');
        $model->status = $req->get('status') ?: false;
        $model->country_id = $req->get('country_id');
        $model->timezone_id = $req->get('timezone_id');
        $model->photo = $req->get('photo');
        $model->verification_code = $req->get('verification_code');
        $model->app_identity = $req->get('app_identity');

        return $model;
    }

    /**
     * @param $q
     * @param string $filterBy
     * @param string $query
     * @return mixed
     */
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
                ->where("username", "ILIKE", "%" . $query . "%")
                ->orWhere('email', "ILIKE", "%$query%");
        }

        return $data;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function getByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * @return string
     */
    public function getPrimaryOrganizationAttribute()
    {
        $org = $this
            ->organizationUser()
            ->where('is_primary', true)
            ->with('organization.application')
            ->first();

        return ($org) ? $org->organization : '';
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getOrganizationById($id)
    {
        return $this->organizationUser()->where('organization_id', $id)->first();
    }

    /**
     * @param array $data
     * @param bool $sendEmail
     * @throws Exception
     */
    public function registerUser(array $data, $sendEmail = true)
    {
        DB::beginTransaction();
        try {

            $app = isset($data['application_id'])
                ? Application::find($data['application_id'])
                : Application::findByName($data['application'])->first();

            if (!$app) {
                throw AppException::inst(
                    'Invalid application specified',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            //create user
            $user = User::create([
                'username' => explode('@', strtolower($data['email']))[0],
                'email' => strtolower($data['email']),
                'password' => bcrypt($data['password']),
                'verification_code' => hash_hmac('sha256', str_random(40), config('app.key')),
                'status' => false,
                'access_type' => 'USER',
                'app_identity' => $app->id
            ]);

            //create organization
            $organization = Organization::create([
                'name' => $data['organization_name'],
                'application_id' => $app->id,
                'phone' => $data['phone'] ?? "",
            ]);

            if ($user && $organization) {
                //create user organization relation
                $organizationUser = OrganizationUser::create([
                    'user_id' => $user->id,
                    'organization_id' => $organization->id,
                    'is_primary' => true,
                    'role_id' => Role::getByName('OWNER')->id
                ]);

                if ($organizationUser->save()) {
                    //create organzitanio
                    $organizationContact = OrganizationContact::inst()
                        ->populate([
                                'email' => strtolower($user->email),
                                'name' => $user->username,
                                'status' => true,
                                'is_primary' => true,
                                'verified_at' => DateTimeUtil::currentMicroSecond(),
                                'organization_id' => $organization->id,
                            ]
                        );

                    if ($organizationContact->save()) {
                        DB::commit();

                        if ($sendEmail) {
                            $myMail = (new VerificationUserMailJob(
                                'Aktivasi Akun Zuragan Anda Sekarang',
                                'Selamat Juragan!',
                                "$organization->name, bisnis anda melalui Zuragan sudah bisa dimulai.",
                                htmlspecialchars(
                                    url("/register/verification?uid=") . $user->verification_code,
                                    ENT_NOQUOTES),
                                $user))->delay(Carbon::now()->addSecond(5));

                            dispatch($myMail);
                        }

                        return $user;
                    }

                    DB::rollback();
                    throw AppException::flash(
                        Response::HTTP_INTERNAL_SERVER_ERROR,
                        'Organization Contact was failed',
                        $organizationContact->errors);
                }

                DB::rollback();
                throw AppException::flash(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    'Organization User was failed',
                    $organizationUser->errors);
            }

            DB::rollback();
            throw AppException::flash(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Organization Or User was failed',
                $user->errors);

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @param array $data
     * @param SocialProviderUser $providerUser
     * @param bool $sendEmail
     * @return User
     * @throws Exception
     */
    public function findOrRegisterSocialUser(array $data,
                                             SocialProviderUser $providerUser,
                                             $sendEmail = true): User
    {
        DB::beginTransaction();
        try {

            $user = $this->getByEmail(strtolower($data['email']));

            //user is exist
            if ($user) {
                $user->providerUser()->save($providerUser);
                DB::commit();
                return $user;
            }

            //create user
            $user = self::inst()
                ->populate([
                    'action' => 'store',
                    'username' => explode('@', strtolower($data['email']))[0],
                    'email' => strtolower($data['email']),
                    'password' => str_random(5),
                    'verification_code' => hash_hmac('sha256', str_random(40), config('app.key')),
                    'status' => true,
                    'access_type' => 'USER',
                    'app_identity' => $data['application_id'],
                    'photo' => $data['photo'] ?: null,
                ])->storeOrFail();

            //create organization
            $organization = Organization::inst()
                ->populate([
                    'action' => 'store',
                    'name' => $data['organization_name'],
                    'application_id' => $data['application_id'],
                ])->storeOrFail();

            //create user organization relation
            OrganizationUser::inst()
                ->populate([
                    'user_id' => $user->id,
                    'organization_id' => $organization->id,
                    'is_primary' => true,
                    'role_id' => Role::getByName('OWNER')->id
                ])->storeOrFail();

            //create organization
            OrganizationContact::inst()
                ->populate([
                        'email' => strtolower($user->email),
                        'name' => $user->username,
                        'status' => true,
                        'is_primary' => true,
                        'verified_at' => DateTimeUtil::currentMicroSecond(),
                        'organization_id' => $organization->id,
                    ]
                )->storeOrFail();

            //send welcome email here maybe

            $user->providerUser()->save($providerUser);
            DB::commit();

            $appName = 'inventory';

            if ($organization->application_id == 1)
                $appName = 'invoice';
            elseif ($organization->application_id == 2)
                $appName = 'inventory';

            $myMail = (new SuccessVerificationUserMailJob($appName, $user))
                ->delay(Carbon::now()->addSecond(3));

            dispatch($myMail);


            $user->organization_id = $organization->id;
            return $user;

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function registerOrganization(array $data)
    {
        DB::beginTransaction();
        try {

            $user = $this->where('email', $data['email'])->firstOrFail();

            if (!Hash::check($data['password'], $user->password)) {
                throw AppException::inst(
                    'wrong input password.',
                    Response::HTTP_BAD_REQUEST);
            }

            $app = Application::findByName($data['application'])
                ->firstOrFail();

            if ($user->hasApplication($app->id)) {
                throw AppException::inst(
                    'User already registered',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $organization = Organization::create([
                'name' => $data['organization_name'],
                'application_id' => $app->id,
            ]);

            if (!$organization) {
                DB::rollback();
                throw AppException::inst(
                    'Create organization failed',
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    $organization->errors);
            }

            $organizationUser = OrganizationUser::inst()->populate([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
                'is_primary' => true,
            ]);

            if (!$organizationUser->save()) {
                DB::rollback();
                throw AppException::inst(
                    'Organization User was failed',
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    $organizationUser->errors);
            }

            $organizationContact = OrganizationContact::inst()->populate([
                'email' => strtolower($user->email),
                'name' => $user->username,
                'status' => true,
                'is_primary' => true,
                'verified_at' => DateTimeUtil::currentMicroSecond(),
                'organization_id' => $organization->id,
            ]);

            if (!$organizationContact->save()) {
                DB::rollback();
                throw AppException::inst(
                    'Organization Contact was failed',
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    $organizationContact->errors);
            }

            DB::commit();
            return $user;

        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @param $verificationCode
     * @return mixed
     * @throws Exception
     * @throws \Throwable
     */
    public function verifyUserExec($verificationCode)
    {
        DB::beginTransaction();
        try {

            $user = $this->where('verification_code', $verificationCode)->first();

            if (!$user) {
                throw AppException::inst(
                    trans('auth.invalid_uuid'),
                    Response::HTTP_BAD_REQUEST
                );
            }

            if ($user->status) {
                throw AppException::inst(
                    trans('auth.account_is_verified'),
                    Response::HTTP_BAD_REQUEST
                );
            }

            $user->status = true;

            if (!$user->save()) {
                DB::rollback();

                throw AppException::inst(
                    trans('auth.verification_email_failed'),
                    Response::HTTP_INTERNAL_SERVER_ERROR, $user->errors);
            }

            if ($user->app_identity === 2) {
                $myMail = (new SuccessVerificationUserMailJob(
                    'inventory',
                    $user))->delay(Carbon::now()->addSecond(3));

                dispatch($myMail);

            } else if ($user->app_identity === 1) {
                $myMail = (new SuccessVerificationUserMailJob(
                    'invoice',
                    $user))->delay(Carbon::now()->addSecond(3));

                dispatch($myMail);
            }

            DB::commit();
            return $user;

        } catch (ClientException $e) {
            DB::rollback();
            Log::error('Client Exception ' . $e->getMessage());
            throw AppException::inst(
                trans('auth.client_exception_verification'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @param $email
     * @throws Exception
     */
    public function resendVerificationEmailExec($email)
    {
        try {

            $user = $this->where('email', strtolower($email))->first();

            if (!$user) {
                throw AppException::inst('Email not found.');
            }

            if ($user->status) {
                throw AppException::inst('Your email is activated.');
            }

            $user->verification_code = hash_hmac('sha256', str_random(40), config('app.key'));

            if (!$user->save()) {
                throw AppException::inst(
                    'failed re-generate code',
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $user->errors);
            }

            //encode to base64 verification code
            $myMail = (new VerificationUserMailJob(
                'Permintaan Ulang Verifikasi Email',
                'Permintaan Ulang Verifikasi Email',
                "Anda melakukan permintaan ulang untuk melakukan verifikasi email. Abaikan email ini jika anda merasa tidak pernah meminta untuk memverfikasi ulang email anda.",
                htmlspecialchars(
                    url("/register/verification?uid=") . $user->verification_code,
                    ENT_NOQUOTES),
                $user))->delay(Carbon::now()->addSecond(5));

            dispatch($myMail);

        } catch (Exception $e) {
            throw $e;
        }

    }

    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function validateUser(Request $request)
    {
        try {

            $user = $this
                ->where('email', strtolower($request->get('username')))
                ->first();

            if (!$user) {
                throw AppException::flash(
                    Response::HTTP_NOT_FOUND,
                    trans('messages.account_not_exist'));
            }

            if (!$user->status) {
                throw AppException::flash(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    trans('auth.account_not_verified'));
            }

            $app = Application::findByName(
                $request->get('application'))
                ->firstOrFail();

            if (!$user->hasApplication($app->id)) {
                throw AppException::flash(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    "You don't have privileges in this application.");
            }

            //get first organization,
            //masih blm dapat mengambil organisasi sesuai keinginan
            $orgUser = $user
                ->getOrganizationByApplicationRef($app->id)
//                ->where('role_id', 1)
                ->first();

            //todo(jee) refactor code
            $organizationUser = OrganizationUser::inst()
                ->with(["role" => function ($q) {
                    return $q->select("id", "name")
                        ->with(['permissions' => function ($q) {
                            return $q->select('name', 'role_id');
                        }]);
                }])
                ->where('organization_id', $orgUser->organization_id)
                ->where('user_id', $user->id)
                ->first();

            $role = $organizationUser->role['name'];
            $permissions = $organizationUser
                ->role
                ->permissions
                ->map(function ($q) {
                    return $q->name;
                });

            unset($organizationUser->role->permissions);

            return [
                'organization_id' => $orgUser->organization_id,
                'user_id' => $user->id,
                'email' => $user->email,
                'username' => $user->username,
                'role' => $role,
                'scopes' => $permissions
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param array $reqData
     * @return bool
     * @throws Exception
     */
    public function changePasswordExec(array $reqData)
    {
        try {

            $user = Auth::User()->fill([
                'password' => bcrypt($reqData['password'])
            ]);

            if (!$user->save()) {
                throw AppException::inst('Change password failed.', Response::HTTP_INTERNAL_SERVER_ERROR, $user->errors);
            }

            return true;

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param array $reqData
     * @param $userData
     * @return bool
     * @throws Exception
     */
    public function changeUserPasswordExec(array $reqData, $userData)
    {
        try {

            $user = $userData->fill([
                'password' => bcrypt($reqData['password'])
            ]);

            if (!$user->save()) {
                throw AppException::inst('Change password failed.', Response::HTTP_INTERNAL_SERVER_ERROR, $user->errors);
            }

            return true;

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * set current organization
     *
     * @param $orgId
     * @return void
     * @throws Exception
     */
    public function setCurrentOrganization($orgId)
    {
        if ($this->hasOrganization($orgId))
            $this->currentOrg = Organization::find($orgId);
        else
            throw AppException::bad("Organization doesn't found");
    }

    /**
     * get current organization
     *
     * @return mixed
     */
    public function getCurrentOrganization()
    {
        return $this->currentOrg;
    }

    /**
     * find availability organization in this organization
     * @param $orgId
     * @return bool
     */
    public function hasOrganization($orgId)
    {
        return $this->organizationUser()
                ->whereHas('organization',
                    function ($q) use ($orgId) {
                        return $q->where('id', $orgId);
                    })->count() > 0;
    }

    /**
     * @param $applicationId
     * @return bool
     */
    public function hasApplication($applicationId)
    {
        return $this->organizationUser()
                ->whereHas('organization', function ($q) use ($applicationId) {
                    return $q->where('application_id', $applicationId);
                })->count() > 0;
    }

    /**
     * @param $applicationId
     * @return mixed
     */
    public function getOrganizationByApplicationRef($applicationId)
    {
        return $this->organizationUser()
            ->whereHas('organization', function ($q) use ($applicationId) {
                return $q->where('application_id', $applicationId);
            });
    }

    /**
     * @param array $data
     * @return bool
     */
    public function checkAvailabilityEmailInApplication(array $data)
    {
        $user = $this->where('email', $data['email'])->first();
        if ($user) {
            $app = Application::findByName($data['application'])->firstOrFail();
            if (!$user->hasApplication($app->id)) {
                return true;
            }
        }
        return false;
    }

    public function getRelatedOrganizations()
    {
        $data = $this
            ->organizationUser()
            ->with(['role' => function ($q) {
                return $q->select('id', 'name');
            }])
            ->with(['organization' => function ($q) {
                return $q->select('id', 'name', 'logo', 'portal');
            }])
            ->get();

        return $data->map(function ($o) {
            $role = $o->role->name;
            unset($o->role);
            $o->name = $o->organization->name;
            $o->logo = $o->organization->multi_res_logo;
            $o->portal = $o->organization->portal;
            $o->role = $role;
            unset($o->user_id);
            unset($o->organization);
            return $o;
        });

    }

    public function getRelatedOrganizationsOpen()
    {
        return $this->organizationUser()
            ->with(
                [
                    'organization' => function ($qu) {
                        return $qu->select(['id', 'name', 'portal', 'logo']);
                    },
                    'user' =>function ($q) {
                        return $q;
                    }
                ]
            )->get();
    }
}
