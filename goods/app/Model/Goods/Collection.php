<?php

namespace App\Model\Goods;
use Illuminate\Support\Facades\DB;
use App\Model\Goods\CollectionRelation;

class Collection extends Model
{
    //指定表名
    protected $table = 'tb_prod_collection';
    protected $relateTable = 'css_prod_sku_relation';
    protected $guarded = [];

    public static $fields = [
        'status',
        'colle_name',
        'colle_desc',
    ];

    public static function getCollectionInfoById($id,$hasProductInfo = true){
        $products = [];
        $ret = Collection::where('id',$id)->first();
        if(!$ret) return false;
        $ret = $ret->toArray();
        $cats = ProductCat::getProductsCatsByIds([$id],2);
        $detail = CollectionDetail::getChannelDetailByCollIds([$id]);
        $detail = $detail[$id]??[];
        if($hasProductInfo) $products = CollectionRelation::getCollectionRelationByCid($id);
        $ret['cats'] = !empty($cats[$id])?array_column($cats[$id],'cat_id'):[];
        $ret['products'] = $products?:[];
        if(!empty($products) && is_array($products)){
            $ret['lowest_ori_price'] = array_sum(array_column($products,'lowest_ori_price'));
            $ret['highest_ori_price'] = array_sum(array_column($products,'highest_ori_price'));
            $ret['lowest_price'] = array_sum(array_column($products,'lowest_price'));
            $ret['highest_price'] = array_sum(array_column($products,'highest_price'));
        }

        $tree = [];
        if($priority_cat_id = $ret['priority_cat_id']){
            Category::getParentsByCatId($priority_cat_id,$tree);
        }

        $ret['priority_cat_tree'] = array_reverse($tree);
        $ret['product_detail_wechat'] = empty($detail['wechat'])?[]:$detail['wechat']['detail'];
        $ret['product_detail_pc'] = empty($detail['pc'])?[]:$detail['pc']['detail'];
        return $ret?:[];
    }

    public static function updateById($id,$upData){
        $upSum = Collection::where('id',$id)->update($upData);
        return $upSum;
    }

}
