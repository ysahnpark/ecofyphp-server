<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EcofyAuthServiceProvider extends ServiceProvider
{
    //protected $defer = true;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Modules\Auth\AuthServiceContract', function(){
            return new \App\Modules\Auth\AuthService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
       return ['App\Modules\Auth\AuthServiceContract'];
    }
}
