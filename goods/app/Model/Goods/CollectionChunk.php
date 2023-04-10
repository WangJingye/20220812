<?php

namespace App\Model\Goods;
use Illuminate\Support\Facades\DB;
use App\Model\Goods\Spu;

class CollectionChunk extends Model
{
    //指定表名
    protected $table = 'tb_collection_chunk';
    protected $relateTable = 'css_prod_sku_relation';
    protected $guarded = [];

    //清除集合的所有赠品
    public static function deleteByCId($colleId){
        return CollectionChunk::where('collection_id',$colleId)->delete();
    }

    //清除集合某块的赠品
    public static function deleteByCIdAndChunkId($colleId,$chunkId){
        return CollectionChunk::where('collection_id',$colleId)->where('chunk_id',$chunkId)->delete();
    }

    public static function getColleChunkInfo($colleId){
        return  CollectionChunk::where('collection_id',$colleId)->get()->toArray();
    }

    /*
     * desc:维护集合赠品
     *  @type 1赠品  0正常商品
     * */
    public static function vindicateCollectionFreebie($colleId,$chunkId,$type = 0){
        return CollectionChunk::updateOrCreate(
            [
                'collection_id'=>$colleId,
                'chunk_id'=>$chunkId
            ],
            ['type'=>$type]
        );
    }

}
