<?php namespace App\Dlc\Coupon\Service;

use Illuminate\Support\Facades\Log;

class BaseService
{
    private static $instance;
    //防止直接创建对象
    private function __construct(){
        $this->init();
    }
    //防止克隆
    private function __clone(){}

    /**
     * 单例模式
     * @return self
     */
    public static function getInstance(){
        $class = get_called_class();
        if (!self::$instance instanceof $class) {
            self::$instance = new $class;
        }
        return self::$instance;
    }

    protected function init(){}













}