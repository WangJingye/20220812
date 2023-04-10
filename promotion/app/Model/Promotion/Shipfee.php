<?php namespace App\Model\Promotion;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class Shipfee extends Model
{
    //指定表名
    protected $table = 'ship_fee';

    public static $default = '默认';

    const CREATED_AT = null;
    const UPDATED_AT = null;

    const CacheKey = 'promotion:ship_fee';

    public static function cacheclean(){
        $all = Shipfee::query()->get();
        foreach($all as $item){
            $data[$item->province] = json_encode([
                'ship_fee'=>(string)sprintf("%.2f",$item->ship_fee),
                'free_limit'=>(string)sprintf("%.2f",$item->free_limit),
                'is_free'=>$item->is_free,
            ]);
        }
        if(isset($data)){
            $old_data = Redis::hgetall(Shipfee::CacheKey);
            $diff_data = array_diff_key($old_data,$data);
            //去掉不需要更新的键值
            if($diff_data){
                //将缓存中的差集删除
                $diff_keys = array_keys($diff_data);
                Redis::hdel(Shipfee::CacheKey,$diff_keys);
                $data = array_diff_key($data,$diff_data);
            }
            if($data){
                Redis::hmset(Shipfee::CacheKey,$data);
            }
        }
    }
}
