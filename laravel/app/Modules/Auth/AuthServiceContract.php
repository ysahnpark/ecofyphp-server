<?php

namespace App\Modules\Auth;

interface AuthServiceContract
{
    /**
     *
     */
    public function name();

    /**
     * Authenticate
     *
     * @param model.Auth  $authCredential - The crediential object
     * @param bool  $createIfNoMatch  - Create an account if no match was found
     * @return object  - Upon success object: [auth, token] is returned
     */
    public function authenticate($oauthUser, $createIfNoMatch);

    /**
     * encode JWT token
     */
    public function encodeToken($account);

    /**
     *
     */
    public function decodeToken($token);
}
