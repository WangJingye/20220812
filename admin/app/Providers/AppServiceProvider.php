<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     * @param UrlGenerator $url
     */
    public function boot(UrlGenerator $url)
    {
        Schema::defaultStringLength(191);
        //左侧菜单
        view()->composer('backend.index',function($view){
            $menus = \App\Model\Menus::getMenus();
            $view->with('menus',$menus);
        });
        if(env('IS_HTTPS')){
            $url->forceScheme('https');
        }
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
