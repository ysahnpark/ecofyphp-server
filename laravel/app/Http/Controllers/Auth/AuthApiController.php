<?php

namespace App\Http\Controllers\Auth;

use Log;
use App\Http\Controllers\Controller;

// Ecofy service
use App\Ecofy\Support\ObjectAccessor;
use App\Modules\Auth\AuthServiceContract;

use App\Modules\Auth\Auth;
use App\Modules\Account\Account;
use App\Modules\Account\Profile;


class AuthApiController extends Controller
{

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function myaccount()
    {
        $account = \Auth::user();
        return \Response::json($account);
    }

    /**
     * Signs in a user with local authentication
     */
    public function signup(AuthServiceContract $authService)
    {
        $payload = \Input::json()->all();

        $models = [];

        $authCredentials = new Auth();
        $authCredentials->authSource = 'local';
        $authCredentials->authId = '-NONE-'; // only applicable for social network
        $authCredentials->username = ObjectAccessor::get($payload, 'auth.username');
        $authCredentials->security_password = ObjectAccessor::get($payload, 'auth.security.password');
        $models['auth'] = $authCredentials;

        $profileModel = new Profile();
        $profileModel->givenName = ObjectAccessor::get($payload, 'profile.givenName');
        $profileModel->familyName = ObjectAccessor::get($payload, 'profile.familyName');
        $profileModel->gender = ObjectAccessor::get($payload, 'profile.gender');
        $emails = ObjectAccessor::get($payload, 'profile.emails');
        $profileModel->dob = new \DateTime(ObjectAccessor::get($payload, 'profile.dob'));
        $models['profile'] = $profileModel;
        Log::info('Parsed $profileModel:' . print_r($profileModel, true));

        $accountModel = new Account();
        $accountModel->kind = 'basic';
        //$accountModel->roles = ;
        $accountModel->status = 'signedup';
        $accountModel->primaryEmail = $emails[0];
        $accountModel->displayName = $profileModel->givenName . ' ' . $profileModel->familyName;
        $models['account'] = $accountModel;
        Log::info('Parsed $accountModel:' . print_r($accountModel, true));

        $auth = $authService->createAccountAndAuth($models);
        $authAndToken = $authService->login($auth);

        return json_encode($authAndToken);
    }

    /**
     * Signs in a user with local authentication
     */
    public function signin(AuthServiceContract $authService)
    {
        $payload = \Input::json();

        $authCredentials = new Auth();
        $authCredentials->authSource = 'local';
        $authCredentials->authId = '-NONE-'; // only applicable for social network
        $authCredentials->username = $payload->get('username');
        $authCredentials->password = $payload->get('password');

        $authAndToken = $authService->authenticate($authCredentials);

        return json_encode($authAndToken);
    }

    public function signout(AuthServiceContract $authService)
    {
        return json_encode(['ok' => true]);
    }


}
