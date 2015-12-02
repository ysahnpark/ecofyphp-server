<?php

namespace App\Http\Middleware;

use Closure;

class EcofyAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $isAjax = $request->ajax();
        $ecofyToken = $request->cookie('ecofy_token');
        $authorization = $request->header('Authorization');

        $authService = \App::make('App\Modules\Auth\AuthServiceContract');
        $accountService = \App::make('App\Modules\Account\AccountServiceContract');

        if (empty($ecofyToken)) {
            $ecofyToken = $authorization;
        }

        $account = null;
        if (!empty($ecofyToken)) {
            $decodedToken = $authService->decodeToken($ecofyToken);
            $account = $accountService->findByPK($decodedToken->id);
            \Auth::login($account);

            /*
            print('---DECODED---');
            print_r($decodedToken);
            print_r($account);
            die();
            */
        }

        if (empty($account)) {
            if ($request->ajax()) {
                return response('Unauthorized', 401);
            } else {
                return redirect()->guest('auth/login');
            }
        }

        return $next($request);
    }
}
