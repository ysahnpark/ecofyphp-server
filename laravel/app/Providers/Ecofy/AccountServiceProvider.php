<?php

namespace App\Providers\Ecofy;

use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Modules\Account\AccountServiceContract', function(){
            return new \App\Modules\Account\AccountService();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
       return ['App\Modules\Account\AccountServiceContract'];
    }
}
