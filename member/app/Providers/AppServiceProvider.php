<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('mobile', function ($attribute, $value, $parameters, $validator) {
            if(preg_match("/^1\d{10}$/",$value)){
                return true;  
            }else{  
                return false; 
            }
        });
        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            if(preg_match("/^1\d{10}$/",$value) || preg_match("/^0\d{2,3}-?\d{7,8}$/", $value)){
                return true;
            }else{
                return false;
            }
        });

        //启用自定义扩展
        \App\Dlc\Coupon\Boot::getInstance()->init();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('ApiRequestInner',function($app){
            return new \App\Service\Dlc\Inner();
        });
    }
}
