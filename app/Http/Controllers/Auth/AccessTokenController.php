<?php
/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */


namespace App\Http\Controllers\Auth;

use App\Cores\Jsonable;
use App\Cores\TokenGenerator;
use App\Exceptions\AppException;
use App\Http\Requests\Auth\IssueTokenRequest;
use App\Http\Requests\SocialLoginRequest;
use App\Models\Application;
use App\Models\SocialProviderUser;
use App\Models\User;
use App\Services\Gateway\Base\BaseServiceContract;
use App\Utils\StringUtil;
use Exception;
use Google_Client;
use GuzzleHttp\Promise;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Http\Controllers\AccessTokenController as BaseAccessTokenController;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;
use Zend\Diactoros\ServerRequest;

/**
 * Class AccessTokenController
 * @package App\Http\Controllers\Auth
 */
class AccessTokenController extends BaseAccessTokenController
{
    use Jsonable, HandlesOAuthErrors;

    /**
     * @param IssueTokenRequest $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function issueUserToken(IssueTokenRequest $request)
    {
        try {
            $httpRequest = request();

            $request->request->replace([
                'username' => strtolower($request->get('username')),
                'grant_type' => $request->get('grant_type'),
                'client_id' => $request->get('client_id'),
                'client_secret' => $request->get('client_secret'),
                'password' => $request->get('password'),
                'scope' => $request->get('scope'),
                'application' => $request->get('application')
            ]);

            $sr = new ServerRequest(
                $request->server->all(),
                [],
                $request->getUri(),
                $request->getMethod(),
                'php://input',
                $request->headers->all(),
                $request->cookies->all(),
                $request->query->all(),
                $request->all(),
                '1.1'
            );

            if (User::inst()->checkAvailabilityEmailInApplication([
                'email' => $request->get('username'),
                'application' => $request->get('application')
            ]))
                return $this->json(Response::HTTP_ACCEPTED, trans('messages.not_registered'));


            if ($httpRequest->grant_type == 'password') {
                $validUser = User::inst()->validateUser($httpRequest);

                $res = $this->issueToken($sr);

                if (!method_exists($res, 'getBody')) {
                    throw AppException::inst(
                        "Oops something when wrong.",
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }

                $res->getBody()->rewind();

                $data = json_decode($res->getBody()->getContents());
                $data->organization_id = $validUser['organization_id'];
                $data->user_id = $validUser['user_id'];
                $data->username = $validUser['username'];
                $data->email = $validUser['email'];
                $data->role = $validUser['role'];
                $data->scopes = $validUser['scopes'];

                if (isset($data->error)) {
                    throw AppException::inst(
                        trans('messages.incorrect_password'),
                        Response::HTTP_BAD_REQUEST,
                        [trans('auth.' . $data->error)]);
                }

                $this->_setDefaultData(
                    $data->access_token,
                    $data->organization_id,
                    $validUser['user_id']);

                return $this->json(
                    Response::HTTP_ACCEPTED,
                    trans('messages.login_succeed'),
                    $data);
            }

            throw AppException::inst(
                trans("auth.incorrect_grand_type"),
                Response::HTTP_BAD_REQUEST);

        } catch (Exception $e) {
            return $this->jsonExceptions($e);
        } catch (\Throwable $e) {
            return $this->jsonExceptions($e);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response|\Laravel\Passport\Http\Controllers\Response
     */
    public function issueToken(ServerRequestInterface $request)
    {
        $response = $this->withErrorHandling(function () use ($request) {
            $input = (array)$request->getParsedBody();
            $clientId = isset($input['client_id']) ? $input['client_id'] : null;
            // Overwrite password grant at the last minute to add support for customized TTLs
            $this->server->enableGrantType(
                $this->makePasswordGrant(), Passport::tokensExpireIn(null, $clientId)
            );

            return $this->server->respondToAccessTokenRequest($request, new Psr7Response);
        });

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            return $response;
        }

        $payload = json_decode($response->getBody()->__toString(), true);

        if (isset($payload['access_token'])) {
            $tokenId = $this->jwt->parse($payload['access_token'])->getClaim('jti');

            $token = $this->tokens->find($tokenId);

            if ($token->client->firstParty()) {
                // We keep previous tokens for password clients
                Log::info('keep alive');
            } else {
                $this->revokeOrDeleteAccessTokens($token, $tokenId);
            }
        }
        return $response;
    }

    /**
     * Create and configure a Password grant instance.
     *
     * @return \League\OAuth2\Server\Grant\PasswordGrant
     */
    private function makePasswordGrant()
    {
        $grant = new \League\OAuth2\Server\Grant\PasswordGrant(
            app()->make(\Laravel\Passport\Bridge\UserRepository::class),
            app()->make(\Laravel\Passport\Bridge\RefreshTokenRepository::class)
        );
        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());
        return $grant;
    }

    /**
     * Revoke the user's other access tokens for the client.
     *
     * @param  Token $token
     * @param  string $tokenId
     * @return void
     */
    protected function revokeOrDeleteAccessTokens(Token $token, $tokenId)
    {
        $query = Token::where('user_id', $token->user_id)->where('client_id', $token->client_id);
        if ($tokenId) {
            $query->where('id', '<>', $tokenId);
        }
        if (Passport::$pruneRevokedTokens) {
            $query->delete();
        } else {
            $query->update(['revoked' => 1]);
        }
    }

    /**
     * handle google login
     *
     * @param SocialLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     * @throws \Throwable
     */
    public function handleSocialLogin(SocialLoginRequest $request)
    {
        try {

            //provider
            $client = new Google_Client([
                'client_id' => $request->get("client_id")
            ]);

            $payload = $client->verifyIdToken($request->get('token'));

            if (!$payload) {
                throw AppException::inst(
                    "Invalid {$request->get('provider')} token param."
                );
            }

            //populate request with random organization name
            $popReq = [
                'email' => $payload['email'],
                'username' => $payload['name'],
                'provider_id' => $payload['sub'],
                'application' => $request->get('application'),
                'organization_name' => StringUtil::uniqrandom(10),//random organization name
                'photo' => $payload['picture']
            ];

            //validate request
            $validator = Validator::make($popReq, [
                'organization_name' => 'required|max:255',
                'email' => 'required|email|max:255',
                'application' => 'required|string|in:invoice,inventory',
            ]);

            //catch error param
            if ($validator->fails()) {
                throw AppException::inst(
                    "Request param error",
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $validator->messages()->all());
            }

            //cek existing user in provider, if no exist create it
            $provUser = SocialProviderUser::firstOrNew([
                'provider' => $request->get('provider'),
                'provider_id' => $payload['sub']
            ]);

            //set application id
            Application::fillApplicationId($popReq);

            if ($provUser->user_id) {
                $user = User::find($provUser->user_id);

                // code sam
                //find user to change status true if he
                // has registered before with manual password
                if ($user->status != true) {
                    $user->status = true;
                    $user->save();
                }
            } else {
                $user = User::inst()
                    ->findOrRegisterSocialUser($popReq, $provUser);
            }

            //create token here
            $token = $user
                ->createToken($request->get('application_cli'))
                ->accessToken;

            $result = [
                'token' => $token,
                'user_id' => $user->id,
                'username' => $user->username,
                'organization_id' => $user
                    ->getOrganizationByApplicationRef(
                        $popReq['application_id']
                    )->first()->organization_id,
                'email' => $user->email
            ];

            $this->_setDefaultData(
                $token,
                $result['organization_id'],
                $result['user_id']);

            return $this->json(
                Response::HTTP_OK,
                "Social authorized.",
                $result
            );

        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param $token
     * @param $orgId
     * @param null $userId
     * @throws Exception
     * @throws \Throwable
     */
    private function _setDefaultData($token, $orgId, $userId = null)
    {
        $service = app(BaseServiceContract::class);

        $service->setHeaders([
            'headers' => [
                'Authorization' => 'Bearer ' . TokenGenerator::inst()->create($orgId, $token, $userId),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Header-Organization-Id' => $orgId,
            ]
        ]);

        $promise = [
            'result' => $service->getAsync(env('GATEWAY_STORE_SERVICE') . "/setup")
        ];

        Promise\unwrap($promise);
    }
}
