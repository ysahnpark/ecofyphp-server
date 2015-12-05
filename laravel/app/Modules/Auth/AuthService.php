<?php

namespace App\Modules\Auth;

use DB;
use Log;

use \Ramsey\Uuid\Uuid;
use \Firebase\JWT\JWT;

use App\Ecofy\Support\ObjectAccessor;

use App\Ecofy\Support\AbstractResourceService;

// Models
use App\Modules\Account\Account;
use App\Modules\Account\Profile;

use App\Modules\Auth\AuthServiceContract;

class AuthService extends AbstractResourceService
    implements AuthServiceContract
{
    public function __construct() {
		parent::__construct('\\App\\Modules\\Auth\\Auth');
	}

    public function name()
    {
        return 'AuthService';
    }

    public function authenticate($oauthUser, $createIfNoMatch)
    {
        $authCredential = $this->buildAuthModel($oauthUser);
        Log::info('Parsed $authCredential:' . print_r($authCredential, true));

        $query = null;
        if ($authCredential->authSource == 'local') {
            $query = Auth::where(function ($query) use ($authCredential) {
                $query->where('authSource', $authCredential->authSource)
                    ->where('username', $authCredential->username);
                });
        } else {
            $query = Auth::where(function ($query) use ($authCredential) {
                $query->where('authSource', $authCredential->authSource)
                    ->where('authId', $authCredential->authId);
                });
        }
        $auth = $query->first();

        Log::info('Fetched Auth:' . print_r($auth, true));
        if (!$auth && $createIfNoMatch) {
            // @todo : make it transactional
            // Insert account and auth records into the database
            $accountModel = $this->buildAccountModel($oauthUser);
            Log::info('Parsed $accountModel:' . print_r($accountModel, true));

            $profileModel = $this->buildProfileModel($oauthUser);
            Log::info('Parsed $profileModel:' . print_r($profileModel, true));

            $this->createAccount($accountModel, $profileModel, $authCredential);
            $auth = $authCredential;
            $auth->account = $accountModel;
        }

        if (!$auth) {
            Log::info('Signin failure');
        }

        // @todo: Update account's lastLogin date

        $retval = [
            'auth' => $auth,
            // @todo: generate JWT token
            // @see https://scotch.io/tutorials/token-based-authentication-for-angularjs-and-laravel-apps
            'token' => $this->encodeToken($auth->account),
        ];

        Log::info('AuthAndToken:' . print_r($retval, true));

        return $retval;
    }

    /**
     * Create account, profile and auth within transaction
     */
    public function createAccount($accountModel, $profileModel, $authCredential)
    {
        DB::transaction(function () use($accountModel, $profileModel, $authCredential) {
            $accountModel->save();
            $profileModel->accountUuid = $accountModel->uuid;
            $profileModel->save();
            $authCredential->accountUuid = $accountModel->uuid;
            $authCredential->save();
        });

    }

    // @todo: factor out to strategy
    function buildAuthModel($oauthUser)
    {
        $authModel = new Auth();
        $authModel->uuid = Uuid::uuid4();
        $authModel->authSource = 'google';
        $authModel->authId = $oauthUser->id;

        $authModel->authCredentialsRaw = json_encode($oauthUser->user);
        $authModel->status = 1;
        $authModel->rememberToken = $oauthUser->token;
        $authModel->security_password = null;
        $authModel->security_activationCode = null;
        $authModel->security_securityQuestion = null;
        $authModel->security_securityAnswer = null;

        return $authModel;
    }

    function buildAccountModel($oauthUser)
    {
        $accountModel = new Account();
        $accountModel->uuid = Uuid::uuid4();

        $accountModel->kind = 'basic';
        // $accountModel->roles = null;
        $accountModel->status = 'registered';
        $accountModel->displayName = $oauthUser->user['displayName'];
        $accountModel->primaryEmail = $oauthUser->email;
        $accountModel->imageUrl = $oauthUser->user['image']['url'];

        return $accountModel;
    }

    function buildProfileModel($oauthUser)
    {
        $profileModel = new Profile();
        $profileModel->uuid = Uuid::uuid4();
        $profileModel->familyName = $oauthUser->user['name']['familyName'];
        $profileModel->givenName = $oauthUser->user['name']['givenName'];
        $profileModel->highlight = ObjectAccessor::get($oauthUser->user, 'braggingRights', null);
        $profileModel->gender = ObjectAccessor::get($oauthUser->user, 'gender', null);
        $profileModel->language = $oauthUser->user['language'];

        return $profileModel;
    }

    /**
     * encode JWT token
     */
    public function encodeToken($account)
    {
        $data = [
            'id' => $account->uuid,
            'kind' => $account->kinds,
            'roles' => $account->roles,
            'displayName' => $account->displayName,
        ];
        $jwt = JWT::encode(
            $data,      //Data to be encoded in the JWT
            env('JWT_SECRET_KEY', 'SuperSecret'), // The signing key
            'HS512'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        );

        return $jwt;
    }

    public function decodeToken($token)
    {
        $decoded = JWT::decode($token, env('JWT_SECRET_KEY', 'SuperSecret'), array('HS512'));
        return $decoded;
    }

    
}
