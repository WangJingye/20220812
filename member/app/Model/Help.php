<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis as RedisClient;

class Help
{
    public static $from = null;

    /*
     * $id = 27-2 解析为27为id，2为类型
     * */
    public static function parsePid($id){
        $tmp = explode('-',$id);
        $pid = $tmp[0];
        $type = $tmp[1]??1;
        return [$pid,$type];
    }

    /**
     * @return array
     * 获取控制器和方法名
     */
    public static function getControllerAndFunction()
    {
        $action = \Route::current()->getActionName();
        list($class, $method) = explode('@', $action);
        $class = substr(strrchr($class,'\\'),1);
        return ['controller' => $class, 'method' => $method];
    }

    public static function Log($desc,$data = [],$path = 'info',$level = 'info'){
        $path = 'logs/' . $path;
        if (!is_dir(storage_path($path))) {
            mkdir(storage_path($path), 0777, true);
        }
        $data_str = is_array($data)?json_encode($data):var_export($data,true);
        error_log(PHP_EOL.'['.date('H:i:s')."] {$desc}：".$data_str.PHP_EOL, 3, storage_path($path . '/' . date('Ymd') . '.log'));
    }

    public static function getFrom($request = null){
        if(!is_null(self::$from)) return self::$from;
        if(is_null($request)) $request = app('request');
        $from = $request->header('from',0)?:($request->from??0);
        //1 小程序 2手机 3PC
        self::$from = in_array($from,[1,2,3])?$from:0;
        return self::$from;
    }

}
