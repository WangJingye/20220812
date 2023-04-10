<?php
namespace App\Service\Redis;

use App\Model\Promotion\Cart;
use App\Service\Redis\Keys;
use Illuminate\Support\Facades\Redis;

//促销
class RedisRules
{
    //同步db中的促销规则到cache
    public static function syncToCache($id,$status){
        $rules = Cart::where('id',$id)->get()->toArray();
        $redis_key = Keys::getRuleKeys();
        foreach($rules as $item){
            $rule_id = $item['id'];
            $item['status'] = $status;
            Redis::HSET($redis_key,$rule_id,json_encode($item));
        }
    }

    //根据rule_ids,从缓存获取数据
    public static function getFromCacheByIds($ids_arr){
        $redis_key = Keys::getRuleKeys();
        $data = Redis::HMGET($redis_key,$ids_arr);
        $return = [];
        foreach($data as $item){
            $item_arr = json_decode($item,true);
            $return[] =$item_arr;
        }
        return $return;
    }
}