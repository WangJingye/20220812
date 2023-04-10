<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis as RedisClient;

class Help
{

    public static $brandCode = null;

    public static function getBrandCode(){
        if(self::$brandCode) return self::$brandCode;
        self::$brandCode = request()->header("brand-code")??'';
        return self::$brandCode ;
    }

    public static function have_special_char($str)
    {
        $length = mb_strlen($str);
        $array = [];
        for ($i=0; $i<$length; $i++) {
            $array[] = mb_substr($str, $i, 1, 'utf-8');
            if( strlen($array[$i]) >= 4 ){
                return true;

            }
        }
        return false;
    }

    public static function formatPrice($price){
        return (string)floatval($price);
    }

    public static function Log($desc,$data = [],$path = 'info',$level = 'info'){
        $path = 'logs/' . $path;
        if (!is_dir(storage_path($path))) {
            mkdir(storage_path($path), 0777, true);
        }
        $data_str = is_array($data)?json_encode($data):var_export($data,true);
        error_log(PHP_EOL.'['.date('H:i:s')."] {$desc}：".$data_str.PHP_EOL, 3, storage_path($path . '/' . date('Ymd') . '.log'));
    }

}
