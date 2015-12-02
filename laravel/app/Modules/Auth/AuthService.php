<?php

namespace App\Modules\Auth;

use DB;
use Log;

use \Ramsey\Uuid\Uuid;
use \Firebase\JWT\JWT;

// Models
use App\Account;
use App\Auth;
use App\Profile;

use App\Modules\Auth\AuthServiceContract;

class AuthService implements AuthServiceContract
{
    public function name()
    {
        return 'AuthService';
    }

    public function authenticate($oauthUser, $createIfNoMatch)
    {
        $authCredential = $this->buildAuthModel($oauthUser);
        Log::info('Parsed $authCredential:' . print_r($authCredential, true));

        $auth = Auth::where(function ($query) use ($authCredential) {
                $query->where('authSource', $authCredential->authSource)
                    ->where('authId', $authCredential->authId);
                })
            ->orWhere(function ($query) use ($authCredential) {
                $query->where('authSource', $authCredential->authSource)
                    ->where('username', $authCredential->username);
                })
            ->first();

        Log::info('Fetched Auth:' . print_r($auth, true));
        if (!$auth && $createIfNoMatch) {
            // @todo : make it transactional
            // Insert account and auth records into the database
            $accountModel = $this->buildAccountModel($oauthUser);
            Log::info('Parsed $accountModel:' . print_r($accountModel, true));

            $profileModel = $this->buildProfileModel($oauthUser);
            Log::info('Parsed $profileModel:' . print_r($profileModel, true));

            DB::transaction(function () use($accountModel, $profileModel, $authCredential) {
                $accountModel->save();
                $profileModel->accountUuid = $accountModel->uuid;
                $profileModel->save();
                $authCredential->accountUuid = $accountModel->uuid;
                $authCredential->save();
                $auth = $authCredential;
                $auth->account = $accountModel;
            });
        }

        if (!$auth) {
            print_r("NOT-FOUND!!");
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
        $profileModel->highlight = $oauthUser->user['braggingRights'];
        $profileModel->gender = $oauthUser->user['gender'];
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

    // Resource Access Operations {{
    /**
     * Add
     *
     * @param Object  $resource - The resource (record) to add
     * @param Object  $options  - Any options for add operation
     * @return Model  - Upon success, return the added model
     */
    public function add($resource, $options)
    {

    }

    /**
     * query
     *
     * @param Object  $criteria - The criteria for the query
     * @param Object  $options  - Any options for query operation
     * @return Array.<Model>  - Upon success, return the models
     */
    public function query($criteria, $options)
    {
        Account::with('profile')->get();
        $records = Auth::all();
    }

    /**
     * query
     *
     * @param Object  $criteria - The criteria for the query
     * @param Object  $options  - Any options for query operation
     * @return number  - Upon success, return count satisfying the criteria
     */
    public function count($criteria, $options)
    {
        Account::where($criteria->property, $criteria->property)->count();
        $records = Auth::all();
    }

    /**
     * Find
     *
     * @param Criteria  $criteria - The crediential object
     * @param object  $options  - Any options for find operation
     * @return Model  - Upon success the model returned
     */
    public function find($criteria, $options)
    {

    }

    /**
     * Find by PK
     *
     * @param mixed  $pk - The primary key of the resource to find
     * @param object  $options  - Any options for find operation
     * @return Model  - Upon success the model returned
     */
    public function findByPK($pk, $options)
    {

    }

    /**
     * Update
     *
     * @param Criteria  $criteria - The crediential object
     * @param object  $resource  - The resource (record) to update
     * @param object  $options  - Any options for update operation
     * @return Model  - Upon success the model returned
     */
    public function update($criteria, $resource, $options)
    {

    }

    /**
     * Remove
     *
     * @param Criteria  $criteria - The crediential object
     * @param object  $options  - Any options for remove operation
     * @return Model  - Upon success the model returned
     */
    public function remove($criteria, $options)
    {

    }

    /**
     * Remove
     *
     * @param mixed  $pk - The primary key of the resource to remove
     * @param object  $options  - Any options for remove operation
     * @return Model  - Upon success the model returned
     */
    public function removeByPK($pk, $options)
    {

    }

    // }} Resource Access Operations
}
