<?php

namespace App\Model\Goods;
use App\Service\Goods\ProductService;
use Illuminate\Support\Facades\DB;
use App\Model\Goods\Spu;
use App\Model\Goods\CollectionChunk;

class CollectionRelation extends Model
{
    //指定表名
    protected $table = 'tb_collection_relation';
    protected $relateTable = 'css_prod_sku_relation';
    protected $guarded = [];

    public static function deleteById($id){
        return CollectionRelation::where('collection_id',$id)->delete();
    }

//    public static function InsertGetId($insertData){
//        dd($insertData);
//        return parent::InsertGetId($insertData);
//    }

    /*
     * Array
(
    [1] => Array
        (
            [skus] => Array
                (
                    [0] => sku1
                    [1] => sku11
                )

            [product_name] => prod1
        )

    [0] => Array
        (
            [skus] => Array
                (
                    [0] => sku2
                    [1] => sku22
                )

            [product_name] => prod2
        )

)
     * */
    public static function insertCollectionRelation($id,$records){
        $records = array_values($records);
        $records = $records?:[];
        CollectionChunk::deleteByCId($id);
        foreach ($records as $k=>$record){
            foreach ($record['skus'] as $skuid){
                $relationData = [
                    'sku_id'=>$skuid,
                    'chunk_id'=>$k,
                    'collection_id'=>$id,
                ];
                $insId = CollectionRelation::insertGetId($relationData);
                if(!$insId) return false;
                $is_freebie = empty($record['is_freebie'])?0:1;
                $insId = CollectionChunk::vindicateCollectionFreebie($id,$k,$is_freebie);
                if(!$insId) return false;
            }
        }
        return true;
    }

    public static function getCollectionRelationByCid($id){
        $chunks = $ret = array();
        $records = DB::table('tb_collection_relation as c')
            ->leftJoin('tb_prod_sku as s', 'c.sku_id', '=', 's.sku_id')
            ->where('c.collection_id', $id)->get()->toArray();
        if(!$records) return [];
//        $pService = new ProductService();

        $productIds = array_column($records,'product_idx');
//        $selectSkuIds = array_column($records,'sku_id');
        $chunkPidMap = array_combine(array_column($records,'chunk_id'),$productIds);
        $products = Spu::batchGetProductsInfoByPid($productIds);
        $chunksInfo = CollectionChunk::getColleChunkInfo($id);
        $isFreebieMap = array_combine(array_column($chunksInfo,'chunk_id'),array_column($chunksInfo,'type'));

//        $ret = array_combine(array_column($records,'chunk_id'),$records);
        foreach($records as $record){
            $record = (array)$record;
            $chunks[$record['chunk_id']][] = $products;
            $chunkSkuIdMap[$record['chunk_id']][] = $record['sku_id'];
        }
        foreach($chunkPidMap as $chunkId=>$pid){
            if(empty($products[$pid])) continue;
            $prod = $products[$pid];
            $spec_type = explode(',',$prod['spec_type'])??[];
            $p_spec = !empty($spec_type)?$spec_type[0]:'';
            $prod['skus'] = $prod['skus']??[];
            //目前一个块只能有一个商品（可以有一个商品的多个规格）
//            if(count(array_unique(array_column($pros,'product_id'))) != 1) continue;
            $ret[$chunkId]['product_name'] = $prod['product_name'];
            $ret[$chunkId]['is_freebie'] = empty($isFreebieMap[$chunkId])?0:1;
            $ret[$chunkId]['chunk_id'] = $chunkId;
            $ret[$chunkId]['display_type'] = $prod['display_type'];

            foreach ($prod['skus'] as $sku){
                $selected = 0;
                if(!empty($chunkSkuIdMap[$chunkId]) && in_array($sku['sku_id'],$chunkSkuIdMap[$chunkId])) $selected =1;
                $sku['selected'] = $selected;
//                $f_sku = $pService->formatSku($sku,$p_spec);
//                $f_sku['selected'] = $selected;
//                $ret[$chunkId]['skus'][] = $pService->formatSku($sku,$p_spec);
                $ret[$chunkId]['skus'][] = $sku;
                $ret[$chunkId]['lowest_ori_price'] = empty($ret[$chunkId]['lowest_ori_price'])?$sku['ori_price']:min($ret[$chunkId]['lowest_ori_price'],$sku['ori_price']);
                $ret[$chunkId]['highest_ori_price'] = empty($ret[$chunkId]['highest_ori_price'])?$sku['ori_price']:max($ret[$chunkId]['highest_ori_price'],$sku['ori_price']);
                $ret[$chunkId]['lowest_price'] = empty($ret[$chunkId]['lowest_price'])?$sku['price']:min($ret[$chunkId]['lowest_price'],$sku['price']);
                $ret[$chunkId]['highest_price'] = empty($ret[$chunkId]['highest_price'])?$sku['price']:max($ret[$chunkId]['highest_price'],$sku['price']);
            };
        }
//        echo json_encode($ret);exit;

        return $ret;
    }


}
