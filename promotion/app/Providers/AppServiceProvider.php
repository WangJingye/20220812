<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        //左侧菜单
        view()->composer('backend.index',function($view){
            $menus = \App\Model\Menus::getMenus();
            $view->with('menus',$menus);
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

    }
}
