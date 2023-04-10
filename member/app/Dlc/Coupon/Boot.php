<?php

namespace App\Dlc\Coupon;

use Illuminate\Console\Application as Artisan;

class Boot
{
    public function init(){
        //路由文件配置
        app()->router->group([
            'namespace' => "App\Dlc\Coupon\Controllers",
            'prefix' => '/coupon',
            'middleware' => ['api']
        ], function ($router) {
            require app_path().'/Dlc/Coupon/route.php';
        });
        $this->commandsInit();
    }

    //命令配置
    protected $commands = [
        \App\Dlc\Coupon\Commands\ShellCommand::class,
    ];

    private static $instance;

    public static function getInstance(){
        $class = get_called_class();
        if (!self::$instance instanceof $class) {
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function commandsInit(){
        foreach($this->commands as $command){
            app()->singleton($command, function ($app) use($command){
                return new $command;
            });
        }
        Artisan::starting(function ($artisan){
            $artisan->resolveCommands($this->commands);
        });
    }




}





