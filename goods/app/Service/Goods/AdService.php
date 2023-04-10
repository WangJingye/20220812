<?php

namespace App\Service\Goods;
use App\Model\Ad\Item;
use App\Model\Ad\Location;
use App\Service\ServiceCommon;
use Illuminate\Support\Facades\Redis;
use App\Model\Goods\Spu;
use App\Model\Goods\Sku;
use App\Model\Goods\Collection;

class AdService extends ServiceCommon
{
    public function getLocAdsFromCache($locs){
        $time = time();
        if(!is_array($locs)) $locs = [$locs];
        $redis = ProductService::getRedis();
        foreach ($locs as $loc){
            $json = $redis->get(config('app.name').':ad:item:list:'.$loc);
            $tmp = json_decode($json,true);
            if(empty($tmp)) continue;
            foreach($tmp as $rec){
                if($rec['start_time']<=$time && $rec['end_time']>$time){
                    $ret[] = $rec;
                }
            }
        }
        return $ret??[];
    }

    public function cacheAllLocAds(){
        $pre_id = 0;
        $ret = [];
        while(true){
            $locs = Location::query()
                ->where('id','>',$pre_id)
                ->get();
            if($locs->isEmpty()){
                break;
            }
            foreach ($locs as $loc){
                $pre_id = max($pre_id,$loc['id']);
                $ret[$loc['title']] = $this->cacheLocAds($loc['id']);
            }
        }
        return $ret;
    }

    public function cacheLocAds($loc_id,$loc = []){
        $time = time();
        $start_time = $time - 600;
        $loc = $loc?:Location::where('id',$loc_id)->first();
        if(empty($loc['title'])) return false;
        $loc_title = $loc['title'];

        $records = Item::where('start_time','<=',$start_time)
            ->where('end_time','>',$time)
            ->where('status',1)
            ->where('loc_id',$loc_id)
            ->orderBy('asort','desc')
            ->orderBy('update_stamp','desc')
            ->get();
        if($records->isEmpty()){
            return false;
        }
        $records = $records->toArray();
        $redis = ProductService::getRedis();
        $key = config('app.name').':ad:item:list:'.$loc_title;
        if($loc->status==0){
            $redis->del($key);
        }else{
            $redis->set($key,json_encode($records));
        }
        return $records;
    }
}
