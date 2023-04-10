<?php

namespace App\Model\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    //指定表名
    protected $table = 'tb_product';
    protected $guarded = [];

    /*
     * desc:根据关键字查询
     * warn:本方法为左右模糊匹配，谨慎使用
     * @params:
     *  $keyword:关键字
     *  $fields:字段
     *  $offset
     *  $limit
     * $retCount 是否需要返回多少行
     */
    public function getRecordsByKeyword($keyword,$fields = [],$offset = 0,$limit = 20,$retCount = false){
        if(!$keyword) return false;
        $fields = $fields?:['product_name'];
        $where = $offLimit = '';
        foreach($fields as $field){
            $where .= $where?' or ':' ';
            $where .= "{$field} like '%{$keyword}%' ";
        }
        if($offset || $limit){
            $offLimit = " limit {$offset},{$limit}";
        }
        $records = DB::select('select * from tb_product where '.$where.$offLimit);
        if($retCount) $count = DB::selectRow('select count(id) as cnt from tb_product where '.$where);

        return ['list'=>$records?:[],'count'=>$count?:0];
    }

    public static function batchGetProductsInfoByPid($productIdxs){
        $ret = array();
        $records = DB::table('tb_product as p')
            ->leftJoin('tb_prod_sku as s', 's.product_idx', '=', 'p.id')
            ->select('p.*','s.sku_id','s.spec_color_code','s.spec_capacity_ml_code','s.ori_price')
            ->whereIn('p.id', $productIdxs)->get()->toArray();
        if(!$records) return [];
//        $ret = array_combine(array_column($records,'chunk_id'),$records);
        foreach($records as $record){
            $record = (array)$record;
            if(empty($ret[$record['id']])) $ret[$record['id']] = $record;
            $ret[$record['id']]['skus'][$record['sku_id']] = [
                'color'=>$record['spec_color_code'],
                'sku_id'=>$record['sku_id'],
                'price'=>$record['ori_price'],
            ];
        }
        return $ret;

    }

}
