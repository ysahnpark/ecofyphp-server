<?php

namespace App\Http\Controllers\Auth;

use Log;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Socialite;

use App\Ecofy\Support\ObjectAccessor;
use App\Modules\Auth\Auth;
use App\Modules\Account\Account;
use App\Modules\Account\Profile;

// Not really required
//use Facebook\Facebook;

// Ecofy service
use App\Modules\Auth\AuthServiceContract;


/**
 * For Socialite documentation
 * @see: https://github.com/laravel/socialite
 */
class AuthFacebookController extends Controller
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
        return Socialite::driver('facebook')->with(['state' => $encoded])->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback(Request $request, AuthServiceContract $authService)
    {
        $authDriver = Socialite::driver('facebook');

        // stateless() is requires so that the Socialite does not try to match
        // with nonce. This is requires becase we are sending custom parameter
        $authDriver->stateless();
        $authDriver->fields(['name', 'email', 'gender', 'verified', 'about'
            , 'first_name', 'last_name', 'middle_name'
            , 'bio', 'birthday', 'education', 'interested_in', 'languages'
            , 'location', 'locale', 'name_format', 'timezone', 'updated_time'
            , 'website', 'work', 'cover']);
        $oauthUser = $authDriver->user();
        $authState = $request->input('state');

        // Obtain the original query string as state
        $state = $this->deserialize($authState, true);

        // DEBUGGING:
        /*
        $userJson = json_encode ($oauthUser, JSON_PRETTY_PRINT);
        print_r($oauthUser);
        die();
        */
        //Log::info($userJson);

        $authCredential = $this->buildAuthModel($oauthUser);

        // @todo: externalize to a AuthService
        $authAndToken = $authService->authenticate($authCredential);
        Log::info('Fetched Auth:' . print_r($authCredential, true));

        if ($authAndToken == FALSE) {

            // @todo : make it transactional
            $models = [];
            $models['auth'] = $authCredential;

            $models['account'] = $this->buildAccountModel($oauthUser);
            Log::info('Parsed $accountModel:' . print_r($models['account'], true));

            $models['profile'] = $this->buildProfileModel($oauthUser);
            Log::info('Parsed $profileModel:' . print_r($models['profile'], true));

            $auth = $authService->createAccountAndAuth($models);
            $authAndToken = $authService->login($auth);
        }

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



    // @todo: factor out to strategy
    function buildAuthModel($oauthUser)
    {
        $authModel = new Auth();
        $authModel->authSource = 'facebook';
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

        $accountModel->kind = 'basic';
        // $accountModel->roles = null;
        $accountModel->status = 'registered';
        $accountModel->displayName = $oauthUser->name;
        $accountModel->primaryEmail = $oauthUser->email;
        $accountModel->imageUrl = $oauthUser->avatar_original;

        return $accountModel;
    }

    function buildProfileModel($oauthUser)
    {
        $profileModel = new Profile();
        $profileModel->familyName = ObjectAccessor::get($oauthUser->user, 'last_name');
        $profileModel->givenName = ObjectAccessor::get($oauthUser->user, 'first_name');
        $profileModel->highlight = ObjectAccessor::get($oauthUser->user, 'about', null);
        $profileModel->gender = ObjectAccessor::get($oauthUser->user, 'gender', null);
        $profileModel->language = ObjectAccessor::get($oauthUser->user, 'locale', null);
        $profileModel->timezone = ObjectAccessor::get($oauthUser->user, 'timezone', null);

        return $profileModel;
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
