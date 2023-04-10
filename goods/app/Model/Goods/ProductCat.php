<?php

namespace App\Model\Goods;
use Illuminate\Support\Facades\DB;

class ProductCat extends Model
{
    //指定表名
    protected $table = 'tb_prod_cat_relation';
    protected $guarded = [];

    /*
     * @params
     *  $product_type 1 商品 2集合
     * */
    public static function getProductCatsByPidx($idx,$product_type = 1){

        $cats = self::getProductsCatsByIds([$idx],$product_type);
        return $cats[$idx]??[];

//        $records = DB::table('tb_prod_cat_relation')
//            ->leftJoin('tb_category', 'tb_category.id', '=', 'tb_prod_cat_relation.cat_id')
//            ->where('tb_prod_cat_relation.product_idx', $idx)
//            ->where('tb_category.cat_type', 1)
//            ->where('tb_prod_cat_relation.type',$product_type)
//            ->get()->toArray();
//
//        foreach($records as $k=>$record){
//            $record = (array)$record;
//            $records[$k] = [
//                'product_idx'=>$record['product_idx'],
//                'cat_name'=>$record['cat_name'],
//                'cat_id'=>$record['cat_id'],
//                'cat_name_en'=>$record['cat_name_en'],
//            ];
//        }

        return $records??[];
    }

    /*
     * $type 1商品 2套装
     * */
    public static function getProductsCatsByIds($ids,$type = 1){
        $records = DB::table('tb_prod_cat_relation as pc')
            ->leftJoin('tb_category as c', 'c.id', '=', 'pc.cat_id')
            ->where('c.status', 1)
            ->whereIn('pc.product_idx',$ids)
            ->where('pc.type',$type)
            ->select('pc.cat_id','pc.product_idx','c.cat_name','c.cat_name_en')
            ->get();
        $records = $records->isEmpty()?[]:object2Array($records);

        foreach($records as $record){
            $ret[$record['product_idx']][] = [
                'product_idx'=>$record['product_idx'],
                'cat_name'=>$record['cat_name'],
                'cat_id'=>$record['cat_id'],
                'cat_name_en'=>$record['cat_name_en'],
            ];
        }

        return $ret??[];

    }

    //批量插入类目商品
    public static function batchInsertCatProducts($id,$pids,$product_type = 1){
        $add_pids = [];
        foreach($pids as $pid){
            try{
                $insert_id = ProductCat::insertGetId(
                    [
                        'product_idx'=>$pid,
                        'cat_id'=>$id,
                        'type'=>$product_type,
                    ]
                );
                if($insert_id) $add_pids[] = $pid;
            }catch(\Exception $e){
                continue;
            }
        }
        return $add_pids;
    }

    public static function getCatProductsById($id){
        $records = DB::table("tb_prod_cat_relation as pc")
            ->leftJoin("tb_product as p",'p.id','=','pc.product_idx')
            ->where("pc.cat_id",$id)
            ->where("pc.type",1)
            ->get();
        return $records->isEmpty()?[]:json_decode(json_encode($records),true);
    }

    public static function getProdAndColleById($id,$limit = 2000,$is_all = 0){
        $records = DB::table("tb_prod_cat_relation")
            ->orderBy('sort','desc')->orderBy('update_time','desc')
            ->limit($limit);
        if($id) $records = $records->where("cat_id",$id);
        $records = $records->get();
        $ret = $product_ids = $collection_ids = $products = $collections = [];
        $records = json_decode(json_encode($records),true);
        foreach($records as $record){
//            $ret['all'][] = ['type'=>$record['type'],'product_idx'=>$record['product_idx']];
            if($record['type'] == 1){
                $product_ids[] = $record['product_idx'];
            }elseif($record['type'] == 2){
                $collection_ids[] = $record['product_idx'];
            }
        }

        $query = new Spu();
        $cQuery = new Collection();
        if($product_ids){
            $products = $query->whereIn('id', $product_ids)->get()->toArray();
            $products = array_combine(array_column($products,'id'),$products);
        }
        if($collection_ids){
            $collections = $cQuery->whereIn('id', $collection_ids)->get()->toArray();
            $collections = array_combine(array_column($collections,'id'),$collections);
        }
        foreach($records as $record){
            if( ($record['type'] == 1) && !empty($products[$record['product_idx']]) ){
                $tmp = $products[$record['product_idx']];
                if(empty($tmp['status']+$is_all)) continue;
                $one = [
                    'product_type'=>1,
                    'product_idx'=>$record['product_idx'],
                    'product_name'=>$tmp['product_name'],
                    'product_desc'=>$tmp['product_desc'],
                    'kv_images'=>$tmp['kv_images'],
                    'short_colle_desc'=>$tmp['product_desc'],
                    'sort'=>$record['sort'],
                    'status'=>$tmp['status'],
                    'product_id'=>$tmp['product_id'],
                    'id'=>$record['id'],
                ];
                $ret['products'][] = $one;
                $ret['all'][] = $one;
            }elseif( ($record['type'] == 2) && $collections[$record['product_idx']] ){
                $tmp = $collections[$record['product_idx']];
                if(empty($tmp['status']+$is_all)) continue;
                $one = [
                    'product_type'=>2,
                    'product_idx'=>$record['product_idx'],
                    'product_name'=>$tmp['colle_name'],
                    'product_desc'=>$tmp['colle_desc'],
                    'kv_images'=>$tmp['kv_images'],
                    'short_colle_desc'=>$tmp['short_colle_desc'],
                    'sort'=>$record['sort'],
                    'status'=>$tmp['status'],
                    'product_id'=>$tmp['colle_id'],
                    'id'=>$record['id'],
                ];
                $ret['collections'][] = $one;
                $ret['all'][] = $one;
            }
        }
        return $ret??[];
    }



    public static function batchGetCatProductsByIds($cat_ids){
        $records = DB::table("tb_prod_cat_relation as pc")
            ->leftJoin("tb_product as p",'p.id','=','pc.product_idx')
            ->where("p.status",1)
            ->whereIn("pc.cat_id",$cat_ids)->get();

        if($records->isEmpty()) return [];
        $ret = [];
        foreach($records as $record){
            $ret[$record['cat_id']][] = $record;
        }

        return $ret;
    }

    public static function updateById($id,$upData){
        $upSum = ProductCat::where('id',$id)->update($upData);
        return $upSum;
    }

}
