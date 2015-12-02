<?php

namespace App\Http\Controllers\Auth;

use DB;
use Log;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Socialite;

use Ramsey\Uuid\Uuid;

// Ecofy service
use App\Modules\Auth\AuthServiceContract;

// Models
use App\Account;
use App\Auth;
use App\Profile;

/**
 * For Socialite documentation
 * @see: https://github.com/laravel/socialite
 */
class AuthGoogleController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider(Request $request)
    {
        // https://developers.google.com/identity/protocols/OAuth2UserAgent

        // Passing the oroginal argument as state
        // @todo pass the nonce
        $input = $request->all();
        $encoded = $this->serialize($input, true);
        return Socialite::driver('google')->with(['state' => $encoded])->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback(Request $request, AuthServiceContract $authService)
    {
        $authDriver = Socialite::driver('google');

        // stateless() is requires so that the Socialite does not try to match
        // with nonce. This is requires becase we are sending custom parameter
        $authDriver->stateless();
        $user = $authDriver->user();
        $authState = $request->input('state');

        // Obtain the original query string as state
        $state = $this->deserialize($authState, true);

        // DEBUGGING:
        /*
        $userJson = json_encode ($user, JSON_PRETTY_PRINT);
        print_r($userJson);
        die();
        */
        //Log::info($userJson);

        // @todo: externalize to a AuthService
        $authAndToken = $authService->authenticate($user,  true);

        if (array_key_exists('redir_url', $state)) {
            // Cookie params:
            $minutes = 365 * 24 * 60;
            $secure = false;
            $httpOnly = false;
            $path = '/';
            $domain = null;
            return redirect($state['redir_url'])
                ->withCookie('ecofy_token', $authAndToken['token']
                    , $minutes , $path, $domain, $secure, $httpOnly);
        }
    }

    /**
     * Serializes an object into JSON
     *
     * @param Object $obj - The object to serialize
     * @param bool   $encode - Whether or not to encode the JSON
     * @return string - JSON serialized string
     */
    function serialize($obj, $encode = false)
    {
        //base64UrlEncode
        $jsonVal = json_encode ($obj);
        if ($encode) {
            return strtr(base64_encode($jsonVal), '+/=', '-_,');
        } else {
            return $jsonVal;
        }
    }

    /**
     * Deserializes JSON-ified string into object
     *
     * @param string $inputStr - The object to serialize
     * @param bool   $decode - Whether or not to decode the JSON
     * @return object - deserialized object
     */
    function deserialize($inputStr, $decode = false)
    {
        // base64UrlDecode
        $temp = $inputStr;
        if ($decode) {
            $temp = base64_decode(strtr($inputStr, '-_,', '+/='));
        }
        return json_decode($temp, true);
    }
}
