<?php namespace App\Services\Dlc;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HelperServices
{
    public static function getPosIdByUid($uid){
        $key = config('app.name').':member:pos_id';
        $pos_id = Redis::hget($key,$uid);
        if(empty($pos_id)){
            //调接口查询
            $resp = app('ApiRequestInner')->request('getPosIdByUid','POST',[
                'uid'=>$uid
            ]);
            if($resp['code']==1){
                $pos_id = array_get($resp,'pos_id')?:'';
            }
        }return $pos_id=='null'?false:$pos_id;
    }

}
