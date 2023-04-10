<?php namespace App\Dlc\Coupon\Service;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

class HelperService extends BaseService
{
    public function log($dir,$data){
        $data = is_string($data)?[$data]:$data;
        //保存到文件
        $path = storage_path('logs/'.$dir.'/');
        $filename = date('Ymd').'.log';
        $_msg = date('[Y-m-d H:i:s]').print_r($data,true);
        if(!is_dir($path)){
            mkdir($path,0755,true);
        }
        file_put_contents($path.'/'.$filename,$_msg.PHP_EOL,FILE_APPEND);
    }














}