<?php
/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */


namespace App\Cores;


use App\Exceptions\AppException;
use App\Models\Organization;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Parser;

class TokenGenerator
{

    public static function inst()
    {
        return new self();
    }

    public function create($orgId, $token, $userId = null)
    {
        try {

            $parser = (new Parser())->parse($token);

            $org = Organization::inst()->getByIdRef($orgId)->firstOrFail();

            if (empty(Auth::User()) && is_null($userId)) {
                throw AppException::inst("Unauthorized user, create token failed.");
            }

            $userData = null;

            if (Auth::User())
                $userData = Auth::User();

            if (!empty($userId))
                $userData = User::find($userId);

            /*
             * Create the token as an array
             */
            $data = [
                'iat' => $parser->getClaim('iat'),          // Issued at: time when the token was generated
                'jti' => $parser->getHeader('jti'),         // Json Token Id: an unique identifier for the token
                'iss' => url('/'),                          // Issuer
                'nbf' => $parser->getClaim('nbf'),          // Not before
                'exp' => $parser->getClaim('exp'),          // Expire
                'data' => [                                // Data related to the signer user
                    'userId' => $userData->userId,                 // userid from the users table
                    'username' => $userData->username,   // User name
                    'fullName' => $userData->fullName, // User name
                    'email' => $userData->email,
                    'organizationId' => $org->id,
                    'organizationName' => $org->name,
                    'organizationPortal' => $org->portal,
                    'organizationLogo' => $org->logo,
                    'address' => $org->address,
                    'phone' => $org->phone,
                    'countryId' => $org->country_id,
                    'provinceId' => $org->province_id,
                    'districtId' => $org->district_id,
                    'regionId' => $org->region_id,
                    'zip' => $org->zip,
                    'application' => $org->application->name,
                ],
                'scopes' => $parser->getClaim('scopes')
            ];

            $secretKey = file_get_contents(Passport::keyPath('oauth-private.key'));

            /*
             * Encode the array to a JWT string.
             * Second parameter is the key to encode the token.
             *
             * The output string can be validated at http://jwt.io/
             */
            $jwt = JWT::encode(
                $data,      //Data to be encoded in the JWT
                $secretKey, // The signing key
                'HS256'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
            );

            return $jwt;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function createToken()
    {
        try {
            $parser = (new Parser())
                ->parse(
                    Auth::user()
                        ->createToken('GatewayToken')
                        ->accessToken
                );

            $org = Auth::User()->getCurrentOrganization();

            /*
             * Create the token as an array
             */
            $data = [
                'iat' => $parser->getClaim('iat'),          // Issued at: time when the token was generated
                'jti' => $parser->getHeader('jti'),         // Json Token Id: an unique identifier for the token
                'iss' => url('/'),                          // Issuer
                'nbf' => $parser->getClaim('nbf'),          // Not before
                'exp' => $parser->getClaim('exp'),          // Expire
                'data' => [                                // Data related to the signer user
                    'userId' => Auth::Id(),                 // userid from the users table
                    'username' => Auth::User()->username,   // User name
                    'fullName' => Auth::User()->full_name, // User name
                    'email' => Auth::User()->email,
                    'organizationId' => $org->id,
                    'organizationName' => $org->name,
                    'organizationPortal' => $org->portal,
                    'organizationLogo' => $org->logo,
                    'address' => $org->address,
                    'phone' => $org->phone,
                    'countryId' => $org->country_id,
                    'provinceId' => $org->province_id,
                    'districtId' => $org->district_id,
                    'regionId' => $org->region_id,
                    'zip' => $org->zip,
                    'application' => $org->application->name,
                ],
                'scopes' => $parser->getClaim('scopes')
            ];


            $secretKey = file_get_contents(Passport::keyPath('oauth-private.key'));

            /*
             * Encode the array to a JWT string.
             * Second parameter is the key to encode the token.
             *
             * The output string can be validated at http://jwt.io/
             */
            $jwt = JWT::encode(
                $data,      //Data to be encoded in the JWT
                $secretKey, // The signing key
                'HS256'// Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
            );

            return $jwt;

        } catch (\Exception $e) {
            throw $e;
        }
    }

}
