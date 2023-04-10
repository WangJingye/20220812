<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class FreeTrial extends Model
{
    const CacheKey = 'orders:free_trial';
    protected $table="free_trial";

    protected $guarded=[];

    public static function getAllCacheData(){
        $trials = Redis::hgetall(self::CacheKey);
        $data = [];
        foreach($trials as $key => $trial){
            $data[$key] = json_decode($trial,true);
        }
        return $data;
    }

    public static function cacheAllData(){
        $trials = self::query()->orderBy('id','desc')->get();
        $data = [];
        foreach($trials as $trial){
            $data[$trial->id] = json_encode([
                'id'=>$trial->id,
                'display_name'=>$trial->display_name,
                'start_time'=>$trial->start_time,
                'end_time'=>$trial->end_time,
                'status'=>$trial->status,
                'add_sku'=>$trial->add_sku,
                'money'=>"{$trial->money}",
                'created_at'=>$trial->created_at->format('Y-m-d H:i:s'),
                'updated_at'=>$trial->updated_at->format('Y-m-d H:i:s'),
            ]);
        }
        if($data){
            $old_data = Redis::hgetall(self::CacheKey);
            $diff_data = array_diff_key($old_data,$data);
            //去掉不需要更新的键值
            if($diff_data){
                //将缓存中的差集删除
                $diff_keys = array_keys($diff_data);
                Redis::hdel(self::CacheKey,$diff_keys);
                $data = array_diff_key($data,$diff_data);
            }
            if($data){
                Redis::hmset(self::CacheKey,$data);
            }
        }
    }
}
