<?php

namespace App\Service\Goods;
use App\Model\Goods\Config;
use App\Model\Goods\Category;
use App\Model\Goods\ProductCat;
use App\Model\Search\Product;
use App\Model\Goods\SalesVolume;
use App\Service\ServiceCommon;
use App\Service\Dlc\HelperService;
use Illuminate\Support\Facades\Redis;
use App\Model\Goods\Spu;
use App\Model\Goods\Sku;
use App\Model\Goods\Collection;
use App\Model\Goods\Spec;
use App\Lib\Http;
use App\Model\Help;
use App\Jobs\ProductQueued as Queued;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Model\Dlc\DlcOmsSyncLog;
use Illuminate\Support\Facades\Log;

class ProductService extends ServiceCommon
{

    public static $redis = null;
    public static $from = null;

    //pdp_rec:pdp推荐,empty_cart_rec:购物车,search_rec:搜索,paid_rec:支付成功,精选商品:selected_rec
    public static $recommendConfigDBName = ['pdp_rec','empty_cart_rec','search_rec','paid_rec','selected_rec'];

    public static function getRedis(){
        if(is_null(self::$redis)){
            self::$redis = Redis::connection('goods');
        }
        return self::$redis;
    }
    //缓存中获取商品信息 （被动缓存）
    public function getProductInfoFromCache($productId,$fields = [],$from = 0){
        $from = $from?:self::getFrom();
        $redis = self::getRedis();
        $key = config('app.name').':product:info:id:'.$productId;
        if( $product = empty($fields)?$redis->hgetall($key):$redis->hmget($key,$fields)){
            $product['skus'] = json_decode($product['skus'],true)??[];
            $product['priority_cat_tree'] = json_decode($product['priority_cat_tree'],true);
            $product['kv_images'] = json_decode($product['kv_images'],true);
            $product['products'] = json_decode($product['products']??'',true);
            $product['cats'] = json_decode($product['cats']??'',true);
            $product['product_detail_wechat'] = json_decode($product['product_detail_wechat']??'',true);
            $product['product_detail_pc'] = json_decode($product['product_detail_pc']??'',true);
        }else{
            $product = $this->cacheProductInfoById($productId);
        }
        if(empty($product)) return [];


        //有来源才返回库存信息
        if($from){
            $max_stock =  0;
            $min_stock = null;
            $sService = new StockService();
            foreach($product['skus'] as $k=>$sku){
                list(,,$stock_info) = $sService->getStockOne($sku['sku_id'],$from);
                $tmp_stock = $stock_info['stock']??0;
                //如果该sku包含打包的sku则获取他们的最小库存
                $all_stock = [$tmp_stock];
                if($sku['include_skus']){
                    foreach($sku['include_skus'] as $kk=>$include_sku){
                        list(,,$include_sku_stock_info) = $sService->getStockOne($include_sku['sku'],$from);
                        $product['skus'][$k]['include_skus'][$kk]['stock'] = $all_stock[] = $include_sku_stock_info['stock']??0;
                    }
                }
                $product['skus'][$k]['stock'] = min($all_stock);
                $product['skus'][$k]['curr_sku_stock'] = $tmp_stock;

                $product['min_stock'] = min($all_stock);
                $product['max_stock'] = max($all_stock);
            }
        }
        if($product){
            //先全部改为从 product_detail_pc 获取
            $product['product_detail'] = $product['product_detail_pc'];
//            $product['product_detail'] = ($from == 3)?$product['product_detail_pc']:$product['product_detail_wechat'];
            unset($product['product_detail_wechat'],$product['product_detail_pc']);
        }
        if(empty($product)) return [];

        if(request()->simple){
            $product = self::simplyProduct($product);
        }

        return $product??[];
    }

    //缓存商品信息
    public function cacheProductInfoById($productId,$addition=[]){
        $redis = self::getRedis();
        $product = Spu::getProductInfoById($productId);
        $product = $this->formatProduct($product);
        if(empty($product)) return false;

        $cache = $product;
        $cache['skus'] = json_encode($product['skus']);
        $cache['unique_id'] = $productId;
        $cache['priority_cat_tree'] = json_encode($product['priority_cat_tree']);
        $cache['kv_images'] = json_encode($product['kv_images']);
        $cache['spec_type'] = json_encode($product['spec_type']);
        $cache['products'] = json_encode($product['products']);
        $cache['cats'] = json_encode($product['cats']);
        $cache['product_detail_wechat'] = json_encode($product['product_detail_wechat']);
        $cache['product_detail_pc'] = json_encode($product['product_detail_pc']);
        $cache = array_merge($cache,$addition);
        $redis->hmset(config('app.name').':product:info:id:'.$productId,$cache);
        return $product;
    }

    //对从数据库中取出的product 格式化
    public function batchFormatProduct($products){
        if(!$products) return [];
        foreach($products as $k=>$product){
            $products[$k] = $this->formatProduct($product);
        }
        return $products;
    }

    public static function simplyProduct($product){
        unset($product['custom_keyword'],
                $product['priority_cat_id'],
//            $product['priority_cat_tree'],
//            $product['skus'],
//                $product['products'],
                $product['product_detail_wechat'],
                $product['product_detail_pc'],
                $product['can_search'],
                $product['cats'],
                $product['kv_image_2'],
            $product['product_detail']
//            $product['kv_images']
        );

        if(!empty($product['kv_images']) && is_array($product['kv_images'])){
            $product['kv_images'] = array_slice($product['kv_images'],0,2);
        }
        if(!empty($product['skus']) && is_array($product['skus'])){
            $product['skus'] = array_slice($product['skus'],0,1);
        }

        return $product;
    }

    //对从数据库中取出的product 格式化
    public function formatProduct($product){
        if(!$product) return [];
        $product['spec_type'] = explode(',',$product['spec_type'] );
        $product['tags'] = explode(',',$product['tag'] );
        $kv_images = is_array($product['kv_images'])?$product['kv_images']:json_decode($product['kv_images'],true);
        $product['kv_image'] = $product['kv_video'] = '';

        $img_info = $this->formatKvImages($kv_images);
        $kv_images_data = $img_info['kv_images_data']??[];
        $img_num = $img_info['img_num']??0;
        $kv_video = $img_info['kv_video']??'';
        $kv_image = $img_info['kv_image']??'';
        $kv_image_2 = $img_info['kv_image_2']??'';
        $kv_video_image = $img_info['kv_video_image']??'';

        $s_type = is_array($product['spec_type'])?$product['spec_type']:explode(',',$product['spec_type']);
        $ret = [
            'id'=>$product['id'],
            'product_id'=>$product['product_id'],
            'unique_id'=>$product['id'],    //配合SEO 将unique_id改为 product_id;
            'product_name'=>$product['product_name'],
            'product_name_en'=>$product['product_name_en'],
            'list_name'=>$product['list_name'],
            'custom_keyword'=>$product['custom_keyword']??'',
            'spec_type'=>$s_type,
            'product_desc'=>$product['product_desc']??"",
            'priority_cat_id'=>$product['priority_cat_id']??0,
            'priority_cat_tree'=>$product['priority_cat_tree']??[],
            'product_type'=>$product['product_type']??1,
            'kv_images'=>$kv_images_data,
            'rec_cat_id'=>$product['rec_cat_id']??'',
            'rec_spu'=>$product['rec_spu']??"",
            'kv_image'=>$kv_image??'',
            'share_img'=>$product['share_img']??'',
            'list_img'=>$product['list_img']??'',
            'list_price'=>$product['list_price']??'',
            'is_gift_box'=>$product['is_gift_box']??'',
            'sort'=>$product['sort']??'',
            'score'=>$product['score']??'',
            'sales'=>$product['sales']??'',
//            'kv_image_2'=>$kv_image_2??'',
            'kv_video'=>$kv_video??'',
            'kv_video_image'=>$kv_video_image,
            'lowest_ori_price'=>!empty($product['lowest_ori_price'])? Help::formatPrice($product['lowest_ori_price']):0,
            'highest_ori_price'=>!empty($product['highest_ori_price'])?Help::formatPrice($product['highest_ori_price']):0,
            'image_show_type'=>$kv_video?3:( ($img_num>=2)?2:1 ),    //1:一张图片 2:两张图片 3:图片、视频各一个
//            'kv_images_data'=>$kv_images_data??[],
//            'detail_images'=>is_array($product['detail_images'])??json_decode($product['detail_images'],true),
            'status'=>$product['status']??0,
            'short_product_desc'=>$product['short_product_desc']??"",
            'skus'=>$product['skus']??[],
            'sku_num'=>count($product['skus']??[]),
//            'type'=>$product['type']??1,    //类型 1正常商品 2预售商品
            'products'=>$product['products']??[],   //固定礼盒有下挂商品
            'product_detail_wechat'=>empty($product['product_detail_wechat'])?[]:(is_array($product['product_detail_wechat']??'')?$product['product_detail_wechat']:json_decode($product['product_detail_wechat'],true)),   //商品微信详情
            'product_detail_pc'=>empty($product['product_detail_pc'])?[]:(is_array($product['product_detail_pc']??'')?$product['product_detail_pc']:json_decode($product['product_detail_pc'],true)),   //商品pc详情
            'cats'=>$product['cats']??[],   //商品所属所以类目
            'lowest_price'=>$product['lowest_price']??0,
            'default_ori_price'=>!empty($product['default_ori_price'])?Help::formatPrice($product['default_ori_price']):0,
            'default_price'=>!empty($product['default_price'])?Help::formatPrice($product['default_price']):0,
            'tag'=>$product['tag']??'',
            'can_search'=>$product['can_search']??1,
            'display_start_time'=>$product['display_start_time']??0,
            'display_end_time'=>$product['display_end_time']??0,
            'display_type'=>$product['display_type']??$s_type[0], //capacity_ml、capacity_g、 color、collection,collection_sku(固定礼盒)
            'highest_price'=>!empty($product['highest_price'])?Help::formatPrice($product['highest_price']):0,
            'updated_at'=>$product['updated_at']??0,
        ];

        return $ret;
    }

    /*
     * $sku信息
     * $p_spec 商品规格维度
     * */
    public function formatSku($sku,$p_spec,$sale_infos = []){
//        $p_spec = 'capacity_ml';
        $default_specs = Spec::batchGetSpecBySpecTypes();
        $spec_map = Sku::SPEC_FIELD_MAP;
        $spec_desc_map = Sku::SPEC_DESC_FIELD_MAP;
        if(!$sale_infos) $sale_infos = $this->getSalesInfo([$sku['product_idx']]);
//        $price = $sku['ori_price'] - ( empty($saleInfos[$sku['sku_id']]['sale'])?0:$saleInfos[$sku['sku_id']]['sale'] );
        $price =  empty($sale_infos[$sku['product_idx']]['discount'])?$sku['ori_price']:($sku['ori_price']*$sale_infos[$sku['product_idx']]['discount']/100) ;
        $price = min($sku['ori_price'],$price);

        $spec_code = '';
        if(empty($sku[$spec_map[$p_spec]??''])){
            $d_spec = [];
        }else{
            $spec_code = $sku[$spec_map[$p_spec]??''];
            $d_spec = empty($default_specs[$p_spec][$sku[$spec_map[$p_spec]]])?[]:$default_specs[$p_spec][$sku[$spec_map[$p_spec]]];
        }

        $spec_desc = $sku[$spec_desc_map[$p_spec]??'']??( $d_spec['spec_desc']??'');
        $spec_property = !empty($d_spec['spec_property'])?$d_spec['spec_property']:($sku[$spec_map[$p_spec]??'']??'');

        $img_info = $this->formatKvImages($sku['sku_kv_images']??$sku['kv_images']);
        $kv_images_data = $img_info['kv_images_data']??[];
        $kv_video = $img_info['kv_video']??'';

        return [
            'color'=>$sku['spec_color_code'],
            'capacity_ml'=>$sku['spec_capacity_ml_code'],
            'capacity_g'=>$sku['spec_capacity_g_code'],
            'spec_desc'=>$spec_desc?:$spec_code,
            'capacity_ml_desc'=>$sku['spec_capacity_ml_code_desc'],
            'capacity_g_desc'=>$sku['spec_capacity_g_code_desc'],
            'sku_id'=>$sku['sku_id'],
            'size'=>$sku['size']??'',
            'ori_price'=>Help::formatPrice($sku['ori_price']),
            'price'=>Help::formatPrice($price),
            'spec_property'=>$spec_property,
            'kv_images'=>$kv_images_data,
            'kv_video'=>$kv_video,
            'revenue_type'=>$sku['revenue_type'],
            'include_skus'=>$this->convertIncludeSkus($sku['include_skus']),
        ];

    }

    public function convertIncludeSkus($include_skus){
        if($include_skus){
            $include_skus = explode(',',$include_skus);
            $data = array_reduce($include_skus,function($result,$item){
                $result[] = [
                    'sku'=>$item,
                    'stock'=>0,
                ];
                return $result;
            });
        }
        return $data??[];
    }

    //从缓存中获取集合信息 (被动缓存)
    public function getColletionInfoFromCache($colleId,$fields = [],$from = ''){
        $redis = self::getRedis();
        $key = config('app.name').':collection:info:'.$colleId;
        if($product = empty($fields)?$redis->hgetall($key):$redis->hmget($key,$fields)){
            $product['skus'] = json_decode($product['skus'],true);
            $product['priority_cat_tree'] = json_decode($product['priority_cat_tree'],true);
            $product['kv_images'] = json_decode($product['kv_images'],true);
            $product['products'] = json_decode($product['products'],true);
            $product['product_detail_wechat'] = json_decode($product['product_detail_wechat'],true);
            $product['product_detail_pc'] = json_decode($product['product_detail_pc'],true);
            $product['cats'] = json_decode($product['cats'],true);
        }else{
            $product = $this->cacheCollectionInfoById($colleId);
        }
        if(!$product) return [];

        //有来源才返回库存信息
        $sService = new StockService();
        $max_stock =  0;
        $min_stock = null;
        foreach($product['products'] as $i=>&$p){

            foreach($p['skus'] as $k=>$sku){
                if(empty($sku['selected'])){
                    unset($p['skus'][$k]);
                    continue;
                }
                if($from){
                    list(,,$stock_info) = $sService->getStockOne($sku['sku_id'],$from);
                    $tmp_stock = $stock_info['stock']??0;
                    $sku['stock'] = $tmp_stock;
                    $min_stock = is_null($min_stock)?$tmp_stock:min($tmp_stock,$min_stock);
                    $max_stock = max($tmp_stock,$max_stock);
                }
                $p['skus'][$k] = $sku;
            }
            $p['skus'] = array_values($p['skus']);
        }
        $product['min_stock'] = $min_stock?:0;
        $product['max_stock'] = $max_stock;

        if($product){
            //先全部改为从 product_detail_pc 获取
            $product['product_detail'] = $product['product_detail_pc'];
//            $product['product_detail'] = ($from == 3)?$product['product_detail_pc']:$product['product_detail_wechat'];
            unset($product['product_detail_wechat'],$product['product_detail_pc']);
        }

        if(empty($product)) return [];

        if(request()->simple){
            $product = self::simplyProduct($product);
        }

        return $product??[];
    }

    //缓存集合信息
    public function cacheCollectionInfoById($colleId){
        $redis = self::getRedis();
        $colleInfo = Collection::getCollectionInfoById($colleId);

        $colleInfo = $this->formatCollection($colleInfo);

        if(empty($colleInfo)) return false;
        $cache = $colleInfo;
        $cache['skus'] = json_encode($cache['skus']);
        $cache['unique_id'] = $colleId.'-2';    //套装 和 商品区分开的唯一ID
        $cache['priority_cat_tree'] = json_encode($cache['priority_cat_tree']);
        $cache['kv_images'] = json_encode($cache['kv_images']);
        $cache['products'] = json_encode($cache['products']);
        $cache['cats'] = json_encode($cache['cats']);
        $cache['product_detail_wechat'] = json_encode($cache['product_detail_wechat']);
        $cache['product_detail_pc'] = json_encode($cache['product_detail_pc']);
        $redis->hmset(config('app.name').':collection:info:'.$colleId,$cache);

//        Product::updateProductsToES([$colleInfo]);


        return $colleInfo;
    }

    public static function checkProductLegal($product){
        if(empty($product['status'])|| ($product['status'] == -1) || empty($product['sku_num']) ) return false;
        if(!empty($product['display_start_time']) && ($product['display_start_time']>time())) return false;
        if(!empty($product['display_end_time']) && ($product['display_end_time']<=time())) return false;

        if( !empty($product['product_type']) && (empty($product['lowest_ori_price']) || empty($product['lowest_price']) || ($product['lowest_ori_price'] == '0.00') || ($product['lowest_price'] == '0.00') ) ) return false;

        return true;
    }

    /*
     * $product 集合信息
     * */
    public function formatCollection($product){
        if(!$product) return [];
        $product['kv_image'] = $product['kv_video'] = '';

        $img_info = $this->formatKvImages($product['kv_images']);
        $kv_images_data = $img_info['kv_images_data']??[];
        $img_num = $img_info['img_num']??0;
        $kv_video = $img_info['kv_video']??'';
//        $kv_image_2 = $img_info['kv_image_2']??'';
        $kv_video_image = $img_info['kv_video_image']??'';

        $ret = [
            'id'=>$product['id'],
//            'unique_id'=>$product['colle_id'],
            'unique_id'=>$product['id'].'-2',
            'product_id'=>$product['colle_id'],
            'product_name'=>$product['colle_name']??'',
            'product_name_en'=>$product['colle_name_en']??'',
            'list_name'=>$product['list_name']??'',
            'custom_keyword'=>$product['custom_keyword']??'',
//            'spec_type'=>$s_type,
            'product_desc'=>$product['colle_desc']??"",
            'priority_cat_id'=>$product['priority_cat_id']??0,
            'priority_cat_tree'=>$product['priority_cat_tree']??[],
//            'product_type'=>$product['product_type']??1,
            'kv_images'=>$kv_images_data,
            'kv_image'=>$img_info['kv_image']??'',
//            'kv_image_2'=>$kv_image_2,
            'kv_video'=>$kv_video,
            'kv_video_image'=>$kv_video_image,
            'rec_cat_id'=>$product['rec_cat_id']??"",
            'image_show_type'=>$kv_video?3:( ($img_num>=2)?2:1 ),    //1:一张图片 2:两张图片 3:图片、视频各一个
//            'kv_images_data'=>$kv_images_data??[],
            'detail_images'=>is_array($product['detail_images'])??json_decode($product['detail_images'],true),
            'status'=>$product['status']??0,
            'short_product_desc'=>$product['short_product_desc']??"",
            'can_search'=>$product['can_search']??1,
            'skus'=>$product['skus']??[],
            'lowest_ori_price'=>Help::formatPrice($product['lowest_ori_price']??0) ,
            'lowest_price'=>Help::formatPrice($product['lowest_price']??0),
            'highest_ori_price'=>Help::formatPrice($product['highest_ori_price']??0) ,
            'highest_price'=>Help::formatPrice($product['highest_price']??0),
            'type'=>$product['product_desc']??1,    //类型 1正常商品 2预售商品
            'products'=>array_values($product['products']??[]),   //固定礼盒有下挂商品
            'sku_num'=>count($product['products']??[]),   //礼盒需要统计商品数量
            'product_detail_wechat'=>empty($product['product_detail_wechat'])?[]:(is_array($product['product_detail_wechat'])?$product['product_detail_wechat']:json_decode($product['product_detail_wechat'],true)),   //商品微信详情
            'product_detail_pc'=>empty($product['product_detail_pc'])?[]:(is_array($product['product_detail_pc'])?$product['product_detail_pc']:json_decode($product['product_detail_pc'],true)),   //商品pc详情
            'product_type'=>2,
            'tag'=>$product['tag']??'',
            'cats'=>$product['cats']??[],
            'display_type'=>"collection" //capacity_ml、capacity_g、 color、collection_sku(固定礼盒)
        ];

        return $ret;
    }

    public function formatKvImages($kv_iamges){
        $kv_images = is_array($kv_iamges)?$kv_iamges:json_decode($kv_iamges,true);
        if(!$kv_images || !is_array($kv_images)) return [];
        $kv_image = $kv_image_2 = $kv_video = $kv_video_image = '';
        $img_num = 0;
        $kv_images_data = [];

        if($kv_images)
            foreach($kv_images as $v){
                if( ($v['tag'] == 'image')  && $v['data']['src']){
                    if(empty($kv_image)) $kv_image =  $v['data']['src']??'';
                    if($kv_image) $kv_image_2 = $v['data']['src']??'';
                    $img_num++;
                }
                if( ($v['tag'] == 'video') && !empty($v['data']['video']) && empty($kv_video)){
                    $kv_video =  $v['data']['video'];
                    $kv_video_image = $v['data']['src']??'';
                }
                if(in_array($v['tag'],['image','video']) && !empty($v['data']['src']) ){
                    $kv_images_data[] = [
                        'tag'=>$v['tag'],
                        'url'=>$v['data']['src'],
                        'video'=>empty($v['data']['video'])?'':$v['data']['video'],
                    ];
                }
            }
         return ['kv_images_data'=>$kv_images_data,'kv_image'=>$kv_image,'kv_image_2'=>$kv_image_2,'img_num'=>$img_num,'kv_video'=>$kv_video,'kv_video_image'=>$kv_video_image];
    }

    //更新集合缓存中部分字段
    public function updateColletioCache($colleId,$upData = []){
        $redis = self::getRedis();
        if(empty($upData)){
            return $this->cacheCollectionInfoById($colleId);
        } else{
            $redis->hmset(config('app.name').':collection:info:'.$colleId,$upData);
        }
        return $upData;
    }

    //从缓存中获取SKU信息
    public function getSkuInfoFromCache($skuId,$fields = []){
        $redis = self::getRedis();
        if(!$skuInfo = empty($fields)?$redis->hgetll():$redis->hmget($fields)){
            $skuInfo = $this->cacheSkuInfoById($skuId);
        }
        return $skuInfo??[];
    }

    //缓存sku信息
    public function cacheSkuSpuMapById($skuId){
        $redis = self::getRedis();
        $skuInfo = Sku::getSkuInfoById($skuId);
        $redis->hmset($skuInfo);
        return $skuInfo;
    }


    //获取sku对应的spu，以便获取sku信息
    public function getSkuSpuMapFromCacheBySkuId($skuIds){
        $redis = self::getRedis();
        $ret = [];

        foreach($skuIds as $skuId){
            $ret[$skuId] = $redis->get(config('app.name').':sku:spu:map:skuid:'.$skuId);
        }

        return $ret;

    }

    //缓存所有的sku对应的spu
    public function cacheAllSkuSpuMap(){
        $pre_id = 0;
        $ret = [];
        while(true){
            $skuModel = new Sku();
            $records = $skuModel->where('id','>',$pre_id)
                ->limit(100)->get();
            if($records->isEmpty()) break;
            $records = $records->toArray();
            foreach($records as $record){
                $pre_id = max($pre_id,$record['id']);
                $ret[$record['sku_id']] = $this->cacheSkuSpuMapBySkuId($record['sku_id'],$record);
            }
        }
        return $ret;
    }

    public function cacheSkuSpuMapBySkuId($skuId,$record = []){
        if($record) $record = Sku::where('sku_id',$skuId)->first()->toArray();
        if(empty($record['product_idx'])) return false;
        $redis = self::getRedis();
        $redis->set(config('app.name').':sku:spu:map:skuid:'.$skuId,$record['product_idx']);
//        echo "{$skuId}对应".$record['product_idx'].PHP_EOL;
        return $record['product_idx'];
    }

    public function cacheAllSpuCodeMap(){
        $data = Spu::query()->get()->pluck('id','product_id')->toArray();
        if($data){
            $redis = self::getRedis();
            $redis->hmset(config('app.name').':spucode:map',$data);
        }
    }

    public function getProductIdList($catId = 0){
        $redis = self::getRedis();
        $data = $redis->get(config('app.name').':product:ids:list');
        $ids = explode(',',$data);
        $ids = array_filter($ids);
        return $ids;

    }




    /*
     * desc:缓存商品列表
     * params:
     *      $needCacheInfo  是否缓存商品信息
     * */
    public function cacheProductIdList($needCacheInfo = true){
        $pre_id = 0;
        $all_ids = [];
        while(true){
            $spu = new Spu();
            $records = $spu->where('id','>',$pre_id)->where('status',1)
                ->limit(100)->get();
            if($records->isEmpty()) break;
            $records = $records->toArray();
            if($needCacheInfo){
                foreach($records as $record){
                    $pre_id = max($record['id'],$pre_id);
                    $this->cacheProductInfoById($record['id']);
                }
            }
            $all_ids = array_merge($all_ids,array_column($records,'id'));
        }
//        echo 'ids:'.json_encode($all_ids);
        if($all_ids){
            $redis = self::getRedis();
            $redis->set(config('app.name').':product:ids:list',implode(',',$all_ids));
        }
        return $all_ids;
    }

    public function cacheCatProductIdList(){
        $cats = Category::getAllCat();
        $ret = [];
        foreach($cats as $cat){
            $ret[$cat['id']] = $this->cacheCatProdIdByCatId($cat['id']);
        }
        return $ret;
    }

    public function cacheAllCatInfo(){
        $cats = Category::getAllCat();
        $ret = [];
        foreach($cats as $cat){
            $ret[$cat['id']] = $this->cacheCatInfoById($cat['id']);
        }
        return $ret;
    }

    public function getCatProdIdListFromCache($id){
        $redis = self::getRedis();
        $res = $redis->get(config('app.name').":cat:product:ids:list:".$id);

        if($res === null){
            $res = $this->cacheCatProdIdByCatId($id);
            $res = json_encode($res);
        }

        $res = json_decode($res,true);
        return $res;
    }

    public function getLastSubCatProdIdList($cat_id){
        $catids = $this->getLastSubCatidFromCache($cat_id);
        if(!$catids) $catids[] = $cat_id;
        $all_pids = $ret = [];
        foreach($catids as $catid){
            $id_infos = $this->getCatProdIdListFromCache($catid);
            foreach($id_infos as $id_info){
                $tmp_id = $id_info['id'].'-'.$id_info['product_type'];
                if(!in_array($tmp_id,$all_pids)) $ret[] = $id_info;
            }
            $all_pids = array_merge($all_pids,$id_infos);
        }
//        $all_pids = array_unique($all_pids);
        return $ret;
    }

    public function cacheCatProdIdByCatId($id){
        $redis = self::getRedis();
        $res = ProductCat::getProdAndColleById($id);
        $freeSkus = Sku::getFreeSkus(); //免费的sku 对应的spu 不能被展示
        $freePids = array_column($freeSkus,'product_idx');
        $cache = [];
        if(!empty($res['all']))
            foreach($res['all']  as $one){
                if(!$one['status']) continue;
                if(($one['product_type'] == 1) && in_array($one['product_idx'],$freePids)) continue;    //免费spu 不被缓存
//                $cache[] = $one['product_idx'].(($one['product_type'] == 2)?'-2':'');
                $cache[] = [
                    'product_type'=>$one['product_type'],
                    'id'=>$one['product_idx'],
                ];
            }
        if($cache) $redis->setex(config('app.name').":cat:product:ids:list:".$id,$cache?config('common.ten_minute'):config('common.a_minute'),json_encode($cache));
        return $cache;
    }

    public function getCatInfoFromCache($id,$sub = false){
        $redis = self::getRedis();
        $key = config('app.name').':cat:info:'.$id;
        if(!$info = json_decode($redis->get($key),true)){
            $info = $this->cacheCatInfoById($id);
        }
        if(!$info) return [];

        $cat_ids = [];
        if($sub){
            $cat_ids = $this->getLastSubCatidFromCache($id);
        }
        $cat_ids = $cat_ids?:[$id];

        $info['product_num'] = 0;
        $all_ids = [];
        foreach($cat_ids as $sub_id ){
//            $ids_key = config('app.name').':cat:product:ids:list:'.$sub_id;
//            $id_infos = $redis->get($ids_key);
//            $id_infos = json_decode($id_infos,true)?:[];
            $id_infos = $this->getCatProdIdListFromCache($sub_id);

            foreach($id_infos as $id_info){
                $all_ids[] = $id_info['id'].(($id_info['product_type'] == 2)?'-2':'');
            }
        }

        $all_ids = array_unique($all_ids);
        $info['product_num'] = count($all_ids);
        return $info;

    }

    //获取最新上架的一匹商品IDs（商品ID、集合ID）
    public  function getLatestPids($num){
        $redis = self::getRedis();
        $key = config('app.name').':latest:ids:num:'.$num;
        if(!$ids = json_decode($redis->get($key),true)){
            $ids = $this->cacheLatestPids($num);
        }
        return $ids??[];
    }

    public function cacheLatestPids($num){
        $spu = new Spu();
        $colle = new Collection();
        $records = $spu->orderby('created_at','desc')->limit($num)->get();
        $records2 = $colle->orderby('created_at','desc')->limit($num)->get();

        if($records->isEmpty() && $records2->isEmpty()) return [];
        $records = $records->toArray();
        $records2 = $records2->toArray();

        $recs = array_merge($records,$records2);

        usort($recs,function($a,$b){
            return $a['created_at'] < $b['created_at'];
        });

        $ret = [];
        foreach($recs as $rec){
            $ret[] = [
                'id'=>$rec['id'],
                'product_type'=>empty($rec['colle_name'])?1:2
            ];
        }

        $ret = array_slice($ret,0,$num);

        $redis = self::getRedis();
        $key = config('app.name').':latest:ids:num:'.$num;
        $redis->setex($key,config('common.five_minute'),json_encode($ret));
        return $ret;
    }


    public function cacheCatInfoById($id){
        $cat = Category::getCatInfoById($id);

        $redis = self::getRedis();
        $key = config('app.name').':cat:info:'.$id;
        $redis->setex($key,config('common.five_minute'),json_encode($cat));
        return $cat??[];
    }

    /*
     * $cats:{
     *  55:[{
     *      'cat_id':1
     *  },
     * {
     *      'cat_id':2
     *  }
     * ]
     * 66:[{
     *      'cat_id':3
     *  },
     * {
     *      'cat_id':24
     *  }
     * ]
     * }
     * */
    public function getSalesInfo($pids,$cats = []){
        $params = [];
        if(!$cats) $cats = ProductCat::getProductsCatsByIds($pids);
        foreach ($pids as $pid){
            $p_cats = $cats[$pid]??[];
            $p_cat_ids = implode(',',array_column($p_cats,'cat_id'));
            $params[] = [
                'cid'=>$p_cat_ids,
                'model_id'=>$p_cat_ids,
                'styleNumber'=>$pid
            ];
        }

        //壮壮提供足迹接口
        $http = new Http();
        //获取商品信息
        $recentBack = $http->curl('promotion/cart/productList', $params);
        $recentBack = is_array($recentBack)?$recentBack:[];
        if(!$recentBack) return [];
        $nums = array_column($recentBack,'styleNumber');
        if(!$nums) return [];
        $recentBack = array_combine($nums,$recentBack);
        $ret = [];
        foreach($pids as $pid){
            $sale = $recentBack[$pid]??[];
            $ret[$pid] = [
                'discount'=>(!empty($sale['type']) && ($sale['discount']) && ($sale['type'] == 'product_discount') )?$sale['discount']:0
            ];
        }
        return $ret;
    }

    public function getRecInfoFromCache($flag){
        $redis = self::getRedis();
        $key = config('app.name').':rec:info:'.$flag;
        if(!$info = json_decode($redis->get($key),true)){
            $info = $this->cacheRecInfoByFlag($flag);
        }
        return $info??[];
    }

    public function cacheRecInfoByFlag($flag){
        $cfg = Config::getConfigByName($flag);
        if(!$cfg) return [];

        $redis = self::getRedis();
        $key = config('app.name').':rec:info:'.$flag;
        $redis->setex($key,config('common.five_minute'),json_encode($cfg));
        return $cfg??[];
    }

    /*
     *
     * */
    public function getProductInfoByUniqueIds($ids,$legal = false,$is_list = 0){
        $from = self::getFrom();
        $ret = [];
        foreach($ids as $id){
            list($pid,$type) = ProductService::parsePid($id);
            if($type == 2)
                $product = $this->getColletionInfoFromCache($pid,[],$from);
            else{
                $product = $this->getProductInfoFromCache($pid,[],$from);
                if($is_list==1){
                    //如果是列表请求则去掉detail内容 不然会数据太大
                    unset($product['product_detail']);
                    unset($product['kv_images']);
                }
            }
            if(!self::checkProductLegal($product)) continue;
            if($product){
                $ret[] = $product;
            }
        }
        return $ret;
    }

    //获取某个目录的叶子节点
    public function getLastSubCatidFromCache($catid){
        $redis = self::getRedis();
        $key = config('app.name').':cat:last:subids:'.$catid;
        if(!$ids = json_decode($redis->get($key),true)){
            $ids = $this->cacheLastSubCatids($catid);
        }
        return $ids?:[];
    }

    //获取某个目录的叶子节点
    public function cacheLastSubCatids($catid){
        $cats = [];
        Category::getChildrenByCatIds([$catid],$cats);
        usort($cats,function($a,$b){    //倒叙排
            if($a['sort'] != $b['sort']) return $a['sort'] < $b['sort'];
            return $a['updated_at'] < $b['updated_at'];
        });
        $ret = [];
        $p_catids = array_column($cats,'parent_cat_id');
        foreach($cats as $cat){
            if(in_array($cat['id'],$p_catids)) continue;
            $ret[] = $cat['id'];
        }

        $redis = self::getRedis();
        $redis->setex(config('app.name').':cat:last:subids:'.$catid,config('common.ten_minute'),json_encode($ret));
        return $ret;
    }


    /*
     * $id = 27-2 解析为27为id，2为类型
     * */
    public static function parsePid($id){
        $tmp = explode('-',$id);
        $pid = $tmp[0];
        $type = $tmp[1]??1;
        if(!empty($tmp[1])){
            $pid = $id;
        }
        return [$pid,$type];
    }

    public static function getFrom($request = null){
        if(!is_null(self::$from)) return self::$from;
        if(is_null($request)) $request = app('request');
        $from = $request->header('from',0)?:($request->from??0);
        //1 小程序 2手机 3PC
        self::$from = in_array($from,[1,2,3])?$from:0;
        return self::$from;
    }

    //SEO 要求不能使用自增ID，只能使用spu，故做一层对应map
    public static function getIdFromProductId($ids){
        $redis = self::getRedis();
        $map = $redis->hmget(config('app.name').':product:id:map',$ids);
        $ret = [];
        foreach($ids as $k=>$id){
            $ret[] = !empty($map[$k])?$map[$k]:$id;  //如果未查询到，返回自己
        }
        return $ret;
    }

    /**
     * 批量更新商品价格[[sku1=>price1],[sku2=>price2]]
     * @param $skus
     * @return bool
     */
    public static function updateBatchPrice($skus){
        try{
            //组装格式
            $multipleData = [];
            foreach($skus as $sku=>$price){
                $multipleData[] = [
                    'sku_id'=>"$sku",
                    'ori_price'=>"$price",
                ];
            }
            $id = DlcOmsSyncLog::query()->insertGetId([
                'content'=>json_encode($skus),
                'type'=>DlcOmsSyncLog::TYPE['price'],
            ]);
            //更新价格
            $r = Sku::updateBatch($multipleData);
            if($r){
                //异步刷新商品缓存
                app(Dispatcher::class)->dispatch(new Queued(
                    'cacheProductInfoBySkuIds',
                    ['skuIds'=>array_keys($skus)])
                );
            }
            DlcOmsSyncLog::query()->find($id)->update(['status'=>$r?1:2]);
            return true;
        }catch (\Exception $e){
            Log::error(__FUNCTION__,[
                'error'=>$e->getMessage(),
            ]);
            return false;
        }
    }

    public function getProductDetailAdInfo($uid = null){
        $ad_name = [
            0=>'product_detail_guest',
            1=>'product_detail_vip',
            2=>'product_detail_vvip',
        ];
        if($uid){
            $resp = app('ApiRequestInner')->request('getUserTypeByUid','POST',compact('uid'));
            $type = array_get($resp,'data.type')?intval($resp['data']['type']):0;
        }else{
            $type = 0;
        }
        $type = (int)$type;
        $ad = HelperService::getAd(array_get($ad_name,$type));
        return array_reduce($ad?:[],function($result,$item){
            $result[] = [
                'image'=>$item['img'],
                'link'=>$item['link'],
            ];
            return $result;
        },[]);
    }

    /**
     * 增加对应spu的销售量
     * @param $params
     * @return bool
     */
    public function addSalesVolume($params){
        if(!empty($params) && is_array($params) && count($params)){
            $skus = array_keys($params);
            $pService = new ProductService();
            $spu_map = $pService->getSkuSpuMapFromCacheBySkuId($skus);
            $data = [];//spu=>qty
            foreach($spu_map as $_sku=>$_spu){
                if($_spu){
                    $qty = (array_get($data,$_spu)?:0)+$params[$_sku];
                    $has = SalesVolume::query()->where('spu_id',$_spu)->count();
                    if($has){
                        SalesVolume::query()->where('spu_id',$_spu)
                            ->increment('volume',$qty);
                    }else{
                        SalesVolume::query()->insert([
                            'spu_id'=>$_spu,
                            'volume'=>$qty,
                        ]);
                    }
                }
            }
        }return true;
    }
}
