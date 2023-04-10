<?php

namespace App\Model\Goods;

use App\Service\Goods\StockService;
use Illuminate\Support\Facades\DB;
use App\Model\Common\YouShu;
use Illuminate\Support\Facades\Log;

class Sku extends Model
{
    //指定表名
    protected $table = 'tb_prod_sku';
    protected $guarded = [];

    //OMS模块中订单队列也使用这个
    const VIRTUAL_PREFIX = 'VIRTUAL_';
    //非真实sku前缀 集合商品会用到(如果主SKU是非真实SKU则创建订单的时候打包的sku使用原价)
    const UNREAL_PREFIX = 'UNREAL_';

    const SPEC_FIELD_MAP = [
        'color' => 'spec_color_code',
        'capacity_ml' => 'spec_capacity_ml_code',
        'capacity_g' => 'spec_capacity_g_code',
    ];

    const SPEC_DESC_FIELD_MAP = [
        'color' => 'spec_color_code_desc',
        'capacity_ml' => 'spec_capacity_ml_code_desc',
        'capacity_g' => 'spec_capacity_g_code_desc',
    ];

    const REVENUE_TYPE_MAP = [
        1 => '护肤用化妆品',
        2 => '护发用化妆品',
        3 => '刷子类制品',
        4 => '美容修饰类化妆品'
    ];

    const YOUSHU_API = [
        1 => 'https://test.zhls.qq.com/data-api/v1/sku/add',
        2 => 'https://test.zhls.qq.com/data-api/v1/salesinfo/add'
    ];

    /*
     * 根据skuId获取Sku信息
     * @params
     *      $skuId 规格ID
     *      $hasProductInfo 是否需要商品信息
     * */
    public static function getSkuInfoById($id, $hasProductInfo = false)
    {
        if ($hasProductInfo) {
            $record = DB::table('tb_prod_sku as s')
                ->leftJoin('tb_product as p', 's.product_idx', '=', 'p.id')
                ->select('p.*', 's.*')
                ->where('s.id', $id)->first();
        } else {
            $record = DB::table('tb_prod_sku')->where('tb_prod_sku.id', $id)->first();
        }
        return (array)$record;
    }

    /*
     * 根据skuIds批量获取Sku信息
     * @params
     *      $skuId 规格ID
     *      $hasProductInfo 是否需要商品信息
     * */
    public static function batchGetSkuInfoBySkuId($skuIds, $hasProductInfo = false)
    {
        if ($hasProductInfo) {
            $records = DB::table('tb_prod_sku as s')
                ->leftJoin('tb_product as p', 's.product_idx', '=', 'p.id')
                ->select('p.*', 'p.kv_images as product_kv_images', 's.*')
                ->whereIn('s.sku_id', $skuIds)->get()->toArray();
        } else {
            $records = DB::table('tb_prod_sku')->whereIn('tb_prod_sku.sku_id', $skuIds)->get()->toArray();
        }
        $records = json_decode(json_encode($records), true);
        $records = array_combine(array_column($records, 'sku_id'), $records);
        return $records ?? [];
    }

    /*
     * 根据skuId获取Sku信息
     * @params
     *      $productId 商品ID
     * */
    public function getSkusInfoByProductId($productId)
    {
        $records = DB::table('css_ec_skus_info')->leftJoin('css_products_info', 'css_ec_skus_info.product_id', '=', 'css_products_info.id')->where('css_ec_skus_info.product_id', $productId)->get()->toArray();
        return $records ?: [];
    }

    public static function getFreeSkus()
    {
        $records = DB::table('tb_prod_sku')->where('ori_price', 0)->get()->toArray();
        return object2Array($records) ?: [];
    }

    public static function getAllStock()
    {

        $goods_name = Spu::pluck('product_name', 'id');
        $skus_arr = Sku::select('sku_id','product_idx','ori_price')->get()->toArray();
        $skus = [];
        $goods = [];
        $goods_price = [];
        foreach ($skus_arr as $item) {
            $skus[] = $item['sku_id'];
            $goods[$item['sku_id']]=$item['product_idx'];
            $goods_price[$item['sku_id']] = $item['ori_price'];
        }


        $stocks = StockService::getStockAll($skus);
        $stock_arr = $stocks[2];
        $csv[] = ['sku', '商品名', '库存数量', '安全库存', '预支库存','备注'];
        
        foreach ($stock_arr as $sku => $item) {
            if(empty($item)){
                $secure = $item['secure'] ?? '0';
                if(isset($goods[$sku]) && isset($goods_name[$goods[$sku]])){
                    $name = $goods_name[$goods[$sku]];
                }else{
                    $name = '';
                }
                $csv[] = [$sku, $name, '0', $secure,'0', ''];
                continue;
            }
            if(!isset($item['stockinc'])){
                $item['stockinc'] = 0;
            }
            if($item['is_share'] == 1){

                $stock = $item['stock'] ?? '0';
            }else{
                $stock = $item['channel1'] +$item['channel2']+$item['channel3'];
            }

            $secure = $item['secure'] ?? '0';
            if($item['stock']==-1&& $item['secure']==1){
                $stock = 0;
                $secure = 0;
            }
          if(isset($goods[$sku]) && isset($goods_name[$goods[$sku]])){

              $name = $goods_name[$goods[$sku]];
          }else{
              $name = '';
          }

            $csv[] = [$sku, $name, $stock, $secure,$item['stockinc'], ''];
        }

        $data = Oms::select('id', 'order_state','order_status')->whereIn('order_state', [1, 5,20])->where('id', '>=', '200114')->get();
        $desc = '';
        foreach ($data as $v) {
            if($v['order_state']>=20 && $v['order_status']!=5){
                continue;
            }
            $items = OmsOrderItem::select('sku', 'collections', 'name','order_sn')->where('order_main_id', $v->id)->get()->toarray();
            foreach ($items as $item) {
                if ($v['order_state'] == 1) {
                    $desc = '未支付';
                }
                if ($v['order_state'] == 5) {
                    $desc = '待审核';
                }
                if ($v['order_state'] == 20) {
                    $desc = '审核拒绝，退款中';
                }

                if ($v['type'] == 1 || $v['type']==3) {
                    $csv[] = [$item['sku'], $item['name'], 1, 0,0, $desc.$item['order_sn']];
                }else{
                    if(empty($v['collections']) || $v['collections'] == '[]'){
                        $csv[] = [$item['sku'], $item['name'], 1, 0,0, $desc.$item['order_sn']];
                    }


                }


            }
        }
        //额外增加 价格
        foreach($csv as $k=>&$v){
            if(!$k){
                $v[6] = '价格';
            }else{
                $v[6] = array_get($goods_price,$v[0]);
            }
        }
        return $csv;
    }

    public static function exportAllStock()
    {

        $goods_name = Spu::pluck('product_name', 'id')->toArray();
        $skus_arr = Sku::select('sku_id','product_idx','ori_price')->get()->toArray();
        $skus = [];
        foreach ($skus_arr as $item) {
            $skus[] = $item['sku_id'];
        }
        $stocks = StockService::getStockAll($skus);
        $stocks = $stocks[2];
        $csv[] = ['SKUID', '商品名', '库存数量','价格'];

        foreach ($skus_arr as $item) {
            $sku = $item['sku_id'];
            $name = $goods_name[$item['product_idx']];
            $stock = array_get($stocks[$item['sku_id']],'stock')?:'0';
            $price = $item['ori_price'];
            $csv[] = [$sku, $name, $stock, $price];
        }
        return $csv;
    }

    /**
     *
     */
    public static function insertStocks($json_str){
       $data = json_decode($json_str,true);

       if(!is_array($data)){
           return false;
       }
       foreach($data as $value){
          Warehose::firstOrCreate($value);
       }
        return true;
    }

    /**
     * 添加/更改商品sku
     */
    public static function taskSku($id)
    {
        $sign = YouShu::getReqSign();
        //查询sku信息
        $sku = DB::table('tb_prod_sku as sku')
                    ->select('sku.sku_id as external_sku_id', 'sku.product_idx as external_spu_id', 'product.status', 'product.product_name as product_name_chinese', 'product.created_at as external_created_time', 'product.kv_images')
                    ->leftJoin('tb_product as product', 'sku.product_idx', '=', 'product.id')
                    ->where('sku.id', $id)
                    ->first();
        $result = [];
        if (!empty($sku)) {
            $img = !empty($sku->kv_images) ? json_decode($sku->kv_images, true) : [];
            $param = json_encode([
                'dataSourceId'      => '10901',
                'skus'              => [
                    [
                        'external_sku_id'   => !empty($sku->external_sku_id) ? (string)$sku->external_sku_id : '',
                        'external_spu_id'   => !empty($sku->external_spu_id) ? (string)$sku->external_spu_id : '',
                        'img_urls'          => [
                            [
                                'primary_imgs'  => [
                                    [
                                        'img_url'   => !empty($img['data']['src']) ? $img['data']['src'] : 'https://image.xxx.cn/product_images/F7FC91EA33A7B465CE3B918759113446.JPG'
                                    ]
                                ],
                                'imgs'          => [
                                    [
                                        'img_url'   => !empty($img['data']['src']) ? $img['data']['src'] : 'https://image.xxx.cn/product_images/F7FC91EA33A7B465CE3B918759113446.JPG'
                                    ]
                                ]
                            ]
                        ],
                        'sales_props'       => [
                            'is_available'  => !empty($sku) && $sku->status == 1 ? true : false
                        ],
                        'desc_props'        => [
                            'product_name_chinese'  => !empty($sku->product_name_chinese) ? $sku->product_name_chinese : ''
                        ],
                        'external_created_time'     => !empty($sku->external_created_time) ? (string) strtotime($sku->external_created_time): (string) time()
                    ]
                ],
            ], true);
            //请求有数api
            $url = self::YOUSHU_API[1] . '?' . $sign;
            $http = new YouShu();
            $result = $http->curl_post($url, $param);
            Log::info('testAddSku:'.json_encode($result, true));
        }
        return $result;
    }

    /**
     * 添加/更新销售信息
     */
    public static function taskSalesInfo($id)
    {
        $sign = YouShu::getReqSign();
        //查询sku信息
        $sku = DB::table('tb_prod_sku as sku')
                    ->select('sku.sku_id as external_sku_id', 'product.status', 'sku.ori_price')
                    ->leftJoin('tb_product as product', 'sku.product_idx', '=', 'product.id')
                    ->where('sku.id', $id)
                    ->first();
        $result = [];
        if (!empty($sku)) {
            $param = json_encode([
                "dataSourceId"  => "10902",
                "salesinfo"     => [
                    [
                        "external_sku_id"   => (string) $sku->external_sku_id,
                        "external_store_id" => "9L11",
                        "price" => [
                            "current_price" => (float) $sku->ori_price,
                            "daily_price"   => (float) $sku->ori_price,
                            "sku_price"     => (float) $sku->ori_price
                        ],
                        "stock" => [
                            "sku_stock_status"  => !empty($sku->status) && $sku->status == 1 ? 1 : 2
                        ],
                        "target_url_props"      => [
                            "miniprogram_appid" => "wx5cdbb2c393858107",
                            "url_miniprogram"   => "pages/pdt-detail/pdt-detail?code=".$id
                        ],
                    ],
                ],
            ], true);
            Log::info('testSalesInfo1:'.$param);
            //请求有数api
            $url = self::YOUSHU_API[2] . '?' . $sign;
            $http = new YouShu();
            $result = $http->curl_post($url, $param);
            Log::info('testSalesInfo:'.json_encode($result, true));
        }
        return $result;
    }

    /**
     * 导入商品sku（有数）历史数据
     */
    public static function exportSkuHistory()
    {
        $sign = YouShu::getReqSign();
        //查询sku信息
        $skuInfo = DB::table('tb_prod_sku as sku')
                    ->select('sku.sku_id as external_sku_id', 'sku.product_idx as external_spu_id', 'product.status', 'product.product_name as product_name_chinese', 'product.created_at as external_created_time', 'product.kv_images')
                    ->leftJoin('tb_product as product', 'sku.product_idx', '=', 'product.id')
                    ->get();
        $result = [];
        if (!empty($skuInfo)) {
            $skus = [];
            foreach ($skuInfo as $k=>$sku) {
                $img = !empty($sku->kv_images) ? json_decode($sku->kv_images, true) : [];
                $skus[] = [
                    'external_sku_id'   => !empty($sku->external_sku_id) ? (string)$sku->external_sku_id : '空',
                    'external_spu_id'   => !empty($sku->external_spu_id) ? (string)$sku->external_spu_id : '空',
                    'img_urls'          => [
                        [
                            'primary_imgs'  => [
                                [
                                    'img_url'   => !empty($img['data']['src']) ? $img['data']['src'] : 'https://image.xxx.cn/product_images/F7FC91EA33A7B465CE3B918759113446.JPG'
                                ]
                            ],
                            'imgs'          => [
                                [
                                    'img_url'   => !empty($img['data']['src']) ? $img['data']['src'] : 'https://image.xxx.cn/product_images/F7FC91EA33A7B465CE3B918759113446.JPG'
                                ]
                            ]
                        ]
                    ],
                    'sales_props'       => [
                        'is_available'  => !empty($sku) && $sku->status == 1 ? true : false
                    ],
                    'desc_props'        => [
                        'product_name_chinese'  => !empty($sku->product_name_chinese) ? (string)$sku->product_name_chinese : '空'
                    ],
                    'external_created_time'     => !empty($sku->external_created_time) ? (string) strtotime($sku->external_created_time): (string) time()
                ];
            }
            $param = json_encode([
                'dataSourceId'      => '10901',
                'skus'              => $skus,
            ], true);
            //请求有数api
            $url = self::YOUSHU_API[1] . '?' . $sign;
            $http = new YouShu();
            $result = $http->curl_post($url, $param);
            Log::info('exportSkuHistory:'.json_encode($result, true));
        }
        return $result;
    }

    /**
     * 导入销售信息（有数）历史数据
     */
    public static function exportSalesInfoHistory()
    {
        $sign = YouShu::getReqSign();
        //查询sku信息
        $skuAll = DB::table('tb_prod_sku as sku')
                    ->select('sku.sku_id as external_sku_id', 'product.status', 'sku.ori_price', 'sku.id')
                    ->leftJoin('tb_product as product', 'sku.product_idx', '=', 'product.id')
                    ->get();
        $result = [];
        if (!empty($skuAll)) {
            $salesinfo = [];
            foreach ($skuAll as $k=>$sku) {
                $salesinfo[] = [
                    "external_sku_id"   => (string) $sku->external_sku_id,
                    "external_store_id" => "9L11",
                    "price" => [
                        "current_price" => (float) $sku->ori_price,
                        "daily_price"   => (float) $sku->ori_price,
                        "sku_price"     => (float) $sku->ori_price
                    ],
                    "stock" => [
                        "sku_stock_status"  => !empty($sku->status) && $sku->status == 1 ? 1 : 2
                    ],
                    "target_url_props"      => [
                        "miniprogram_appid" => "wx5cdbb2c393858107",
                        "url_miniprogram"   => "pages/pdt-detail/pdt-detail?code=".$sku->id
                    ],
                ];
            }
            $param = json_encode([
                "dataSourceId"  => "10902",
                "salesinfo"     => $salesinfo,
            ], true);
            //请求有数api
            $url = self::YOUSHU_API[2] . '?' . $sign;
            $http = new YouShu();
            $result = $http->curl_post($url, $param);
            Log::info('exportSalesInfoHistory:'.json_encode($result, true));
        }
        return $result;
    }

    public static function updateBatch($multipleData = [])
    {
        try {
            if (empty($multipleData)) {
                Log::info("批量更新数据为空");
                return false;
            }
            $tableName = 'tb_prod_sku'; // 表名
            $firstRow = current($multipleData);

            $updateColumn = array_keys($firstRow);
            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $referenceColumn = isset($firstRow["id"]) ? "id" : current($updateColumn);
            unset($updateColumn[0]);
            // 拼接sql语句
            $updateSql = "UPDATE " . $tableName . " SET ";
            $sets = [];
            $bindings = [];
            foreach ($updateColumn as $uColumn) {
                $setSql = "`" . $uColumn . "` = CASE ";
                foreach ($multipleData as $data) {
                    $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn];
                }
                $setSql .= "ELSE `" . $uColumn . "` END ";
                $sets[] = $setSql;
            }
            $updateSql .= implode(", ", $sets);
            $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings = array_merge($bindings, $whereIn);
            $whereIn = rtrim(str_repeat("?,", count($whereIn)), ",");
            $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
            Log::info($updateSql);
            // 传入预处理sql语句和对应绑定数据
            DB::update($updateSql, $bindings);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
