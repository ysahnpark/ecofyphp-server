<?php

namespace App\Http\Controllers\Auth;

use Log;
use Socialite;
use Illuminate\Routing\Controller;

class AuthGoogleController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('google')->user();

        $userJson = json_encode ($user);
        print_r($userJson);
        Log::info($userJson);
    }
}
