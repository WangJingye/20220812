<?php
/**
 * 保存配置项
 */
if (!function_exists('option_set')) {
    function option_set($name,$value){
        return \App\Model\Options::query()->where('option_name',$name)->updateOrInsert(['option_name'=>$name],['option_value'=>serialize($value)]);
    }
}

/**
 * 获取配置项
 */
if (!function_exists('option_get')) {
    function option_get($name){
        static $options;
        if(!isset($options[$name])){
            $options[$name] = \App\Model\Options::query()->where('option_name',$name)->value('option_value');
        }
        return $options[$name];
    }
}

/**
 * 通过数组获取配置项
 */
if (!function_exists('option_get_with')) {
    function option_get_with($array){
        $model = \App\Model\Options::query()->whereIn('option_name',$array);
        return $model->count()?$model->pluck('option_value')->toArray():[];
    }
}

/**
 * OSS是否启用
 */
if (!function_exists('oss_status')) {
    function oss_status(){
        static $oss_status;
        if(is_null($oss_status)){
            $oss_status = \App\Model\ConfigOss::checkWork();
        }
        return $oss_status;
    }
}

/**
 * 获取OSS地址
 */
if (!function_exists('get_oss_path')) {
    function get_oss_path(){
        static $oss_path;
        if(is_null($oss_path)){
            $oss = \App\Model\ConfigOss::query()->first();
            $oss_path = $oss->url?($oss->url.'images/'):'';
        }
        return $oss_path;
    }
}

/**
 * 获取OSS域名
 */
if (!function_exists('get_oss_url')) {
    function get_oss_url(){
        static $oss_url;
        if(is_null($oss_url)){
            $oss_url = App\Model\ConfigOss::find(1)->url;
        }
        return $oss_url;
    }
}

if (!function_exists('nick_encode')) {
    function nick_encode($nick){
        return (mb_substr($nick, 0,1).'*****')?:'';
    }
}

/**
 * 记录日志
 */
if (!function_exists('log_json')) {
    function log_json($dir,$method,$msg){
        $path = public_path('logs/'.$dir.'/'.$method.'/');
        $filename = date('Ymd').'.log';
        $msg = date('[Y-m-d H:i:s]').$msg;
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        file_put_contents($path.'/'.$filename,$msg.PHP_EOL,FILE_APPEND);
    }
}
if(!function_exists('is_time_cross')){
    function is_time_cross($beginTime1 = '', $endTime1 = '', $beginTime2 = '', $endTime2 = '') {
        $beginTime1 = strtotime($beginTime1);
        $endTime1 = strtotime($endTime1);
        $beginTime2 = strtotime($beginTime2);
        $endTime2 = strtotime($endTime2);
        $status = $beginTime2 - $beginTime1;
        if ($status > 0) {
            $status2 = $beginTime2 - $endTime1;
            if ($status2 >= 0) {
                return false;
            } else {
                return true;
            }
        } else {
            $status2 = $endTime2 - $beginTime1;
            if ($status2 > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
    
}





