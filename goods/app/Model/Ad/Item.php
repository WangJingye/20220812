<?php

namespace App\Model\Ad;
use App\Service\Goods\ProductService;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    //指定表名
    protected $table = 'tb_ad_item';
    protected $guarded = [];
    const UPDATED_AT = null;
    const CREATED_AT = null;

    public static $fields = [
        'status',
        'name',
        'img',
        'link',
        'start_time',
        'end_time',
        'data1',
        'data2',
        'data3',
        'data4',
        'data5',
        'data6',
        'data7',
        'data8',
        'data9',
        'data10',
        'userid',
        'state',
        'asort',
        'img_size',
    ];

    /**
     * 获得此产品的SKU.
     */
    public function skus()
    {
        return $this->belonhasManygsToMany('App\Model\Goods\Location', $this->relateTable, 'product_idx', 'sku_idx');
    }

    public static function getSkusByProductId($productId){
        $record = DB::table('css_ec_skus_info')->leftJoin('css_products_info', 'css_ec_skus_info.product_id', '=', 'css_products_info.id')->where('css_ec_skus_info.sku', $skuId)->get()->toArray();
    }

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

    public static function batchGetProductsInfoByPid($productIdxs,$retSkus = true){
        $ret = array();
        $query = DB::table('tb_product as p');
        if($retSkus){
            $query = $query->leftJoin('tb_prod_sku as s', 's.product_idx', '=', 'p.id')
                ->select('p.*','s.sku_id','s.spec_color_code','s.spec_capacity_ml_code','s.spec_capacity_g_code','s.spec_color_code_desc','s.spec_capacity_ml_code_desc','s.spec_capacity_g_code_desc','s.ori_price');
        }

        $records = $query->whereIn('p.id', $productIdxs)->get()->toArray();
        if(!$records) return [];
//        $ret = array_combine(array_column($records,'chunk_id'),$records);
        $pService = new ProductService();
        $all_specs = array_keys(Spec::SPEC_NAME_MAP);
        foreach($records as $record){
            $record = (array)$record;
            $specs = $record['spec_type']?explode(',',$record['spec_type']):[];
//            var_dump($specs,$record['spec_capacity_ml_code_desc']);
            $spec_desc = '';
            $spec_desc = in_array('color',$specs)?$record['spec_color_code_desc']:$spec_desc;
            $spec_desc = in_array('capacity_ml',$specs)?$spec_desc.'&'.$record['spec_capacity_ml_code_desc']:$spec_desc;
            $spec_desc = in_array('capacity_g',$specs)?$spec_desc.'&'.$record['spec_capacity_g_code_desc']:$spec_desc;
            $spec_desc = trim($spec_desc,'&');
            if(empty($ret[$record['id']])) $ret[$record['id']] = $record;
            if($retSkus && $record['sku_id'])
                $ret[$record['id']]['skus'][$record['sku_id']] = [
                    'color'=>$record['spec_color_code_desc'],
                    'capacity_ml'=>$record['spec_capacity_ml_code_desc'],
                    'capacity_g'=>$record['spec_capacity_g_code_desc'],
                    'spec_desc'=>$spec_desc,
                    'sku_id'=>$record['sku_id'],
                    'price'=>$record['ori_price'],
                ];
        }
        return $ret;

    }

//    public static function batchGetProductsInfoByPid($productIds){
//        $ret = array();
//        $records = DB::table('css_products_info')
//            ->leftJoin('css_ec_skus_info', 'css_ec_skus_info.product_id', '=', 'css_products_info.product_id')
//            ->whereIn('css_products_info.product_id', $productIds)->get()->toArray();
//        if(!$records) return [];
////        $ret = array_combine(array_column($records,'chunk_id'),$records);
//        foreach($records as $record){
//            $record = (array)$record;
//            if(empty($ret[$record['product_id']])) $ret[$record['product_id']] = $record;
//            $ret[$record['product_id']]['skus'][$record['sku']] = [
//                'color'=>$record['color'],
//                'sku_id'=>$record['sku'],
//                'price'=>$record['price'],
//            ];
//        }
//        return $ret;
//    }

    public static function getProductInfoById($id,$retSkus = true){
        $products = self::batchGetProductsInfoByPid([$id],$retSkus);
        return $products[$id]??[];

//        $records = DB::table('tb_product as p')
//            ->leftJoin('tb_prod_sku as s', 's.product_idx', '=', 'p.id')
//            ->select('p.*','s.sku_id','s.spec_color_code','s.spec_capacity_ml_code','s.ori_price')
//            ->where('p.id', $id)->get()->toArray();
//        if(!$records) return [];
////        $ret = array_combine(array_column($records,'chunk_id'),$records);
//        foreach($records as $record){
//            $record = (array)$record;
//            if(empty($record['sku_id'])) continue;
//            $skus[$record['sku_id']] = [
//                'color'=>$record['spec_color_code'],
//                'sku_id'=>$record['sku_id'],
//                'ori_price'=>$record['ori_price'],
//            ];
//        }
//        $ret = (array)$records[0];
//        $ret['skus'] = $skus??[];
//        return $ret;
    }

    public static function updateById($id,$upData){
        $upSum = Item::where('id',$id)->update($upData);
        return $upSum;
    }

    public static function deleteById($id){
        return Item::query()->find($id)->delete();
    }

    public static function getProductCats(){

    }

}
