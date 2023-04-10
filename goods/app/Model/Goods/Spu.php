<?php

namespace App\Model\Goods;
use App\Service\Goods\ProductService;
use Illuminate\Support\Facades\DB;
use App\Model\Common\YouShu;
use Illuminate\Support\Facades\Log;

class Spu extends Model
{
    //指定表名
    protected $table = 'tb_product';
    protected $guarded = [];
    public static $fields = [
        'status',
        'product_name',
    ];

    const YOUSHU_API = [
        1 => 'https://test.zhls.qq.com/data-api/v1/spus/add'
    ];

    /**
     * 获得此产品的SKU.
     */
    public function skus()
    {
        return $this->belonhasManygsToMany('App\Model\Goods\Sku', $this->relateTable, 'product_idx', 'sku_idx');
    }

    public static function getSkusByProductId($productId){
        $record = DB::table('css_ec_skus_info')->leftJoin('css_products_info', 'css_ec_skus_info.product_id', '=', 'css_products_info.id')->where('css_ec_skus_info.sku', $skuId)->get()->toArray();
    }

    public function sales()
    {
        return $this->hasOne('App\Model\Goods\SalesVolume', 'spu_id', 'product_id');
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
        $ret = $sku_ids = $details = array();
        $from = ProductService::getFrom();
        $query = DB::table('tb_product as p');
        if($retSkus){
            $query = $query->leftJoin('tb_prod_sku as s', 's.product_idx', '=', 'p.id')
                ->leftJoin('sales_volume as sv', 'sv.spu_id', '=', 'p.id')
                ->select('p.*','s.id as sku_idx','s.kv_images as sku_kv_images','s.sku_id','s.include_skus','s.revenue_type','s.contained_sku_ids','s.spec_color_code','s.spec_capacity_ml_code','s.product_idx','s.spec_capacity_g_code','s.spec_color_code_desc','s.spec_capacity_ml_code_desc','s.spec_capacity_g_code_desc','s.ori_price','p.share_img','p.list_img','p.is_gift_box','p.sort','sv.volume as sales');
        }
        $records = $query->whereIn('p.id', $productIdxs)->get()->toArray();
        $cats = ProductCat::getProductsCatsByIds($productIdxs);
        $cats = $cats??[];
        $details = SpuDetail::getChannelDetailByPids($productIdxs);
        if(!$records) return [];
        $sku_ids = array_column($records,'sku_id');
        $sku_ids = array_unique(array_filter($sku_ids));
        $pService = new ProductService();
        if($retSkus) $saleInfos = $pService->getSalesInfo($productIdxs,$cats);
        foreach($records as $record){
            $record = (array)$record;
            if(empty($ret[$record['id']]['id'])) $ret[$record['id']] = $record;
            if($retSkus && $record['sku_id']){
                $specs = $record['spec_type']?explode(',',$record['spec_type']):[];
                //当前只显示一个规格
                $p_spec = !empty($specs[0])?trim($specs[0]):'';
                //自定义信泽字段
                $ret[$record['id']]['share_img'] = $record['share_img'];
                $ret[$record['id']]['list_img'] = $record['list_img'];
                $ret[$record['id']]['is_gift_box'] = $record['is_gift_box'];
                $ret[$record['id']]['sort'] = $record['sort'];
                $ret[$record['id']]['sales'] = intval($record['sales']);
                //固定礼盒的，一个SPU只允许一个SKU
                if(!empty($ret[$record['id']]['product_type'])) continue;
                if($record['contained_sku_ids']) {
                    $ret[$record['id']]['product_type'] = 3;    //商品类型 1普通商品  2商品集合 3固定礼盒
                    $contained_sku_ids = explode(',',$record['contained_sku_ids']);
                    $contained_skus = Sku::batchGetSkuInfoBySkuId($contained_sku_ids,true);
                    $tmpSaleInfos = $pService->getSalesInfo($contained_sku_ids); //优惠信息
                    foreach($contained_sku_ids as $contained_sku_id){
                        $contained_sku = $contained_skus[$contained_sku_id]??[];
                        if(!$contained_sku) continue;

                        $tmp = [];
                        $tmp['product_name'] = $contained_sku['product_name'];
                        $tmp_kv_images_data = $pService->formatKvImages($contained_sku['product_kv_images']);
                        $tmp['kv_image'] = $tmp_kv_images_data['kv_image']??'';

                        $tmp_specs = $contained_sku['spec_type']?explode(',',$contained_sku['spec_type']):[];
                        //当前只显示一个规格
                        $tmp_p_spec = !empty($tmp_specs[0])?trim($tmp_specs[0]):'';
                        $tmp['display_type'] = $tmp_p_spec;
                        $tmp_sku = $pService->formatSku($contained_sku,$tmp_p_spec,$tmpSaleInfos);
                        $tmp['skus'][] = $tmp_sku;
                        $cont_skus[] = $tmp;
                    }
                    $ret[$record['id']]['products'] = $cont_skus;
                    $ret[$record['id']]['display_type'] = 'collection_sku';
                }
                $tmp_sku = $pService->formatSku($record,$p_spec,$saleInfos);

                $ret[$record['id']]['skus'][] = $tmp_sku;
                if(empty($ret[$record['id']]['default_ori_price'])){
                    $ret[$record['id']]['default_ori_price'] = $record['ori_price'];
                }
                if(empty($ret[$record['id']]['default_price'])) $ret[$record['id']]['default_price'] = $tmp_sku['price'];
                $ret[$record['id']]['cats'] = !empty($cats[$record['id']])?array_column($cats[$record['id']],'cat_id'):[];
                $ret[$record['id']]['display_type'] = !empty($ret[$record['id']]['display_type'])?$ret[$record['id']]['display_type']:$p_spec;
                $ret[$record['id']]['product_detail_wechat'] = empty($details[$record['id']]['wechat'])?[]:$details[$record['id']]['wechat']['detail'];
                $ret[$record['id']]['product_detail_pc'] = empty($details[$record['id']]['pc'])?[]:$details[$record['id']]['pc']['detail'];

                $sku_ids[] = $record['sku_id'];
                //商品最低原价
                $ret[$record['id']]['lowest_ori_price'] = (string)floatval(empty($ret[$record['id']]['lowest_ori_price'])?$record['ori_price']:min($ret[$record['id']]['lowest_ori_price'],$record['ori_price']));
                $ret[$record['id']]['highest_ori_price'] = (string)floatval(empty($ret[$record['id']]['highest_ori_price'])?$record['ori_price']:max($ret[$record['id']]['highest_ori_price'],$record['ori_price']));
                $ret[$record['id']]['lowest_price'] = (string)floatval(empty($ret[$record['id']]['lowest_price'])?$tmp_sku['price']:min($ret[$record['id']]['lowest_price'],$tmp_sku['price']));
                $ret[$record['id']]['highest_price'] = (string)floatval(empty($ret[$record['id']]['highest_price'])?$tmp_sku['price']:max($ret[$record['id']]['highest_price'],$tmp_sku['price']));
                //使用50ml的价格作为list price
                if(array_get($record,'spec_capacity_ml_code_desc')=='50ml'){
                    $ret[$record['id']]["list_price"] = array_get($record,'ori_price');
                }
            }
        }

        $pidx = reset($productIdxs);
        //列表价格
        if(empty($ret[$pidx]["list_price"]) && !empty($ret[$pidx]['lowest_ori_price'])){
            $ret[$pidx]["list_price"] = $ret[$pidx]['lowest_ori_price'];
        }

        foreach($ret as $pid=>$one){
            //没有优先目录，取商品挂载的第一个目录
            $priority_cat_id = $ret[$pid]['priority_cat_id']??0;
            if(!$priority_cat_id){
                $p_cats = ProductCat::getProductCatsByPidx($pid);
                $cat_ids = array_column($p_cats,'cat_id');
                //后改为 类目ID 最大的，因为底层的类目ID都比较大
                $priority_cat_id = $cat_ids?max($cat_ids):0;
            }
            $tree = [];
            if($priority_cat_id){
                Category::getParentsByCatId($priority_cat_id,$tree);
            }
            $ret[$pid]['priority_cat_tree'] = array_reverse($tree);
            $ret[$pid]['priority_cat_id'] = $priority_cat_id;
        }
        return $ret;

    }

    public static function getProductInfoById($id,$retSkus = true){
        $products = self::batchGetProductsInfoByPid([$id],$retSkus);
        return $products[$id]??[];
    }

    public static function getProductInfoByProductId($product_id,$retSkus = false){
        $product = Spu::where('product_id',$product_id)->first();
        if(!$product) return false;
        $products = self::batchGetProductsInfoByPid([$product['id']],$retSkus);
        return $products[$product['id']]??[];

    }


    public static function updateById($id,$upData){
        $upSum = Spu::where('id',$id)->update($upData);
        return $upSum;
    }

    public static function getProductCats(){

    }

    /**
     * 添加/更改商品spu
     */
    public static function taskSpu($id)
    {
        $sign = YouShu::getReqSign();
        //查询spu信息
        $product_name = DB::table('tb_product')
                            ->where('id', $id)
                            ->value('product_name');
        $result = [];
        if (!empty($product_name)) {
            $param = json_encode([
                'dataSourceId'      => '10900',
                "spus"  => [
                    [
                        "external_spu_id"   => (string) $id,
                        "desc_props"        => [
                            "product_name_chinese"  => !empty($product_name) ? $product_name : ''
                        ]
                    ]
                ]
            ], true);
            //请求有数api
            $url = self::YOUSHU_API[1] . '?' . $sign;
            $http = new YouShu();
            $result = $http->curl_post($url, $param);
            Log::info('testAddSpu:'.json_encode($result, true));
        }
        return $result;
    }

    /**
     * 导入spu（有数）历史数据
     */
    public static function exportSpuHistory()
    {
        $sign = YouShu::getReqSign();
        //查询spu信息
        $spuInfo = DB::table('tb_product')
                        ->select('product_name', 'id')
                        ->get();
        $result = [];
        if (!empty($spuInfo)) {
            $spus = [];
            foreach ($spuInfo as $k=>$spu) {
                $spus[] = [
                    "external_spu_id"   => (string) $spu->id,
                    "desc_props"        => [
                        "product_name_chinese"  => !empty($spu->product_name) ? $spu->product_name : '空'
                    ]
                ];
            }
            $param = json_encode([
                'dataSourceId'      => '10900',
                "spus"              => $spus
            ], true);
            //请求有数api
            $url = self::YOUSHU_API[1] . '?' . $sign;
            $http = new YouShu();
            $result = $http->curl_post($url, $param);
            Log::info('testAddSpu:'.json_encode($result, true));
        }
        return $result;
    }
}
