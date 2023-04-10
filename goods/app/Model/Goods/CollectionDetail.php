<?php

namespace App\Model\Goods;
use App\Service\Goods\ProductService;
use Illuminate\Support\Facades\DB;

class CollectionDetail extends Model
{
    //指定表名
    protected $table = 'tb_collection_detail';
    protected $guarded = [];

    protected $fillable = [];

    const CHANNELS = [
        'wechat',
        'pc',
    ];

    public static function getDetailsByPid($pid){
        $query = DB::table('tb_prod_collection as p')
                ->leftJoin('tb_collection_detail as pd', 'pd.product_idx', '=', 'p.id')
                ->where('p.id',$pid)
                ->select('p.kv_images','pd.*')->get();
        $recs = $query->isEmpty()?[]:object2Array($query);

        $ret = [];
        foreach($recs as $rec){
            if(!$ret) $ret['kv_images'] = $rec['kv_images'];
            $ret[$rec['channel']] = $rec['detail'];
        }

        return $ret;
    }

    public static function getChannelDetailByCollIds($pids){
//        if(!in_array($channel,self::CHANNELS)) $channel = self::CHANNELS[0];
        $recs = DB::table('tb_collection_detail')
            ->whereIn('product_idx',$pids)
//            ->where('channel',$channel)
            ->get();
        $recs = $recs->isEmpty()?[]:object2Array($recs->toArray());
        if(!$recs) return [];
        $ret = [];
        foreach($recs as $rec){
            $ret[$rec['product_idx']][$rec['channel']] = $rec;
        }
        return $ret;
    }

}
