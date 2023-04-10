<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder as ESClientBuilder;

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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 注册一个名为 es 的单例
        $this->app->singleton('es', function () {
            // 从配置文件读取 Elasticsearch 服务器列表
            $builder = ESClientBuilder::create()->setHosts([
                [
                    'host'   => config("database.connections.elasticsearch.hosts"),
                    'port'   => '9200',
                    'scheme' => config("database.connections.elasticsearch.scheme"),
                    'user'   => config("database.connections.elasticsearch.user"),
                    'pass'   => config("database.connections.elasticsearch.pass")
                ]
            ])->setConnectionPool('\Elasticsearch\ConnectionPool\SimpleConnectionPool', []);
            // 如果是开发环境
            if (app()->environment() === 'local') {
                // 配置日志，Elasticsearch 的请求和返回数据将打印到日志文件中，方便我们调试
                $builder->setLogger(app('log'));
            }

            return $builder->setRetries(10)->build();
        });

        $this->app->bind('ApiRequestInner',function($app){
            return new \App\Service\Dlc\Inner();
        });
    }
}
