<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ApiRequestInner',function($app,$params){
            return new \App\Services\ApiRequest\Inner(array_get($params,'module'));
        });
    }
}
