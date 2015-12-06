<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

// Ecofy service
use App\Modules\Auth\AuthServiceContract;

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
        return json_encode($account);
    }

    /**
     * Signs in a user with local authentication
     */
    public function signup(AuthServiceContract $authService)
    {
        $payload = \Input::json();
        $payload->username;
        $payload->password;

        $authCredentials = new Auth();
        $authCredentials->authSource = 'local';
        $authCredentials->authId = '-NONE-'; // only applicable for social network
        $authCredentials->username = $payload->username;
        $authCredentials->password = $payload->password;

        $authService->authenticate();
    }

    /**
     * Signs in a user with local authentication
     */
    public function signin(AuthServiceContract $authService)
    {
        $payload = \Input::json();
        $payload->username;
        $payload->password;

        $authCredentials = new Auth();
        $authCredentials->authSource = 'local';
        $authCredentials->authId = '-NONE-'; // only applicable for social network
        $authCredentials->username = $payload->username;
        $authCredentials->password = $payload->password;

        $authService->authenticate();
    }

    public function signout(AuthServiceContract $authService)
    {
        return json_encode(['ok' => true]);
    }
}
