<?php

namespace App\Http\Controllers\Outward\Goods;

use App\Http\Controllers\Api\Controller;
use App\Model\Search\SearchES;
use App\Service\Goods\AdService;
use App\Service\Goods\CategoryService;
use App\Service\Goods\ProductService;
use App\Service\UsersService;
use Illuminate\Http\Request;
use App\Model\Goods\Spu;
use App\Model\Goods\ProductHelp;
use App\Model\Goods\Tree;
use App\Lib\Http;
use App\Model\Goods\Category;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getProductList(Request $request){
        try{
            $fields = [
                'cat_id' => 'required',
                'from' => 'integer',
                'row_product_num' => 'integer',
                'page' => 'integer',
            ];
            $validator = Validator::make($request->all(), $fields, [
                    'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
                ]
            );
            if($validator->fails()){
                return $this->error($validator->errors()->first());
            }

            $page = $request->page??1;
            $row_product_num = max(2,$request->row_product_num??3);
            $from = ProductService::getFrom($request);
            $cat_id = $request->cat_id??0;

            $page_size = 12;
            $sub = $request->sub??0;    //是否是拿叶子类目节点的商品
            $pService = new ProductService();
            $adService = new AdService();
            $start = ($page-1)*$page_size;
            $end = $page*$page_size;

            $cat = $pService->getCatInfoFromCache($cat_id);
            if(empty($cat['status'])){
                return $this->error(0,"类目已下架");
            }

            $ads = $adService->getLocAdsFromCache('product_list_ad');
            $ads = array_filter($ads,function($ad)use($cat_id,$start,$end){
                if(($ad['data2'] == $cat_id) && $start<=$ad['data1'] && $ad['data1']<$end ) return true;
                return false;
            });

            $ads = array_combine(array_column($ads,'data1'),$ads);
            //是否是拿叶子类目节点的商品
            $id_infos = $sub?$pService->getLastSubCatProdIdList($cat_id):$pService->getCatProdIdListFromCache($cat_id);
            $id_infos = $id_infos?:[];
            //过滤重复
            $tmp = [];
            $id_infos = array_filter($id_infos,function($one) use(&$tmp){
                if(in_array($one['id'].'-'.$one['product_type'],$tmp)) return false;
                $tmp[] = $one['id'].'-'.$one['product_type'];
                return true;
            });

            if(!$id_infos) return $this->success(['pageData'=>[],'count'=>0]);
            //解决队尾插入广告问题
            $id_infos[] = [
                'product_type'=>4,
                'id'=>0,
            ];

            //完成广告插入
            $final_id_infos = $pre_ad = [];
            $tmp = 1;
            foreach($id_infos as $k=>$v){
                $loc = $k+1;
                $ok = 0;
                $insert = false;
                if($tmp%$row_product_num + 1 <= $row_product_num ) $ok = 1;

                if($pre_ad){
                    $final_id_infos[] = $pre_ad;
                    $final_id_infos[] = $v;
                    $tmp += 3;
                    $insert = true;
                }

                if( !empty($ads[$loc]) ){
                    $ads[$loc]['product_type'] = 3;   //广告类型
                    if($ok) {
                        $final_id_infos[] = $ads[$loc];
                        $final_id_infos[] = $v;
                        $tmp += 3;
                        $insert = true;
                    }else{
                        $pre_ad = $ads[$loc];
                    }
                }
                if(!$insert) $final_id_infos[] = $v;
            }

            $count = count($final_id_infos);
            $page_id_infos = array_slice($final_id_infos,$start,$page_size);

            $ret = $products = [];
            foreach($page_id_infos as $id_info){
                $id = $id_info['id'];
                $product_type = $id_info['product_type'];
                if($product_type == 2){
                    if($product = $pService->getColletionInfoFromCache($id,[],$from)){
                        // 下架 || 无SKU
                        if(!ProductService::checkProductLegal($product)) continue;
//                        if($simple) $product = ProductService::simplyProduct($product);
                        $products[] = $product;
                    }
                }elseif($product_type == 3){
                    $ad = $id_info;
                    if($ad['data3'] && $ad['data4']) $adProduct = $pService->getProductInfoFromCache($ad['data4']);
                    //插入广告数据
                    if(!empty($adProduct)){
                        $adProduct['show_type'] = 2;
                        $adProduct['kv_image'] = $ad['img']??$adProduct['kv_image'];
                        $adProduct['link'] = ($from == 1)?$ad['data6']:$ad['link']; //小程序链接 配置在data6
//                        $adProduct['link'] = $ad['link']??(config('common.dlc_host').'detail?id='.$adProduct['id']);
                        $products[] = $adProduct;
                    }else{
                        $products[] = [
                            'show_type'=>2,
                            'kv_image'=>$ad['img'],
                            'link'=>($from == 1)?$ad['data6']:$ad['link'],
                            'product_desc'=>$ad['data5'],
                        ];
                    }
                }elseif($product_type == 1){
                    if($product = $pService->getProductInfoFromCache($id,[],$from)){
                        // 下架 || 无SKU  || 价格为0
                        if(!ProductService::checkProductLegal($product)) continue;
//                        if($simple) $product = ProductService::simplyProduct($product);
                        $products[] = $product;
                    }
                }
            }
            $ret['cat_name'] = $cat['cat_name'];
            $ret['pageData'] = $products;
            $ret['count'] = $count;
            $ret['curPage'] = $page;
            $ret['totalPage'] = ceil($count/$page_size);

            return $this->success($ret);
        }catch (\Exception $e){
            return $this->error(0,$e->getMessage());

        }
    }

    public function hotSale(Request $request){
        $from = ProductService::getFrom($request);
        $adService = new AdService();
        $ads = $adService->getLocAdsFromCache('hot_sale_product');
        $ret = [];

        foreach($ads as $ad){
            if(empty($ad['name']) || empty($ad['img'])) continue;
            $link = ($from == 1)?$ad['link']:$ad['data1'];
            $ret[] = [
                'title'=>$ad['name'],
                'img'=>$ad['img'],
                'link'=>$link,
            ];
        }

        return $this->success($ret);
    }


    /*
     * @params
     *  ids = 3,5-2,7
     *      5-2代表套装，且id为5
     * */
    public function getProduct(Request $request){
        $ids = $request->ids;
        $from = ProductService::getFrom($request);
        $ids = explode(',',$ids);
        if(!$ids) return $this->error(0,"参数缺失");

        $pService = new ProductService();

        $ret = [];
        foreach($ids as $id){
            list($pid,$type) = ProductService::parsePid($id);
            if($type == 2)
                $product = $pService->getColletionInfoFromCache($pid,[],$from);
            else
                $product = $pService->getProductInfoFromCache($pid,[],$from);
            if($product){
                // 下架 || 无SKU
                if(!ProductService::checkProductLegal($product)) continue;
                $ret[] = $product;
            }
        }
        $ret['adInfo'] = [];
        return $this->success($ret);
    }

    //根据集合ID，查询集合信息
    public function getCollectionInfoByColleId(Request $request){
        $id = $request->id;
        if($id) $this->error(0,"参数id缺失");
        $pService = new ProductService();
        $colle = $pService->getColletionInfoFromCache($id);
        $this->success($colle);
    }

    //根据sku ID 查询商品信息
    public function getProductInfoBySkuId(Request $request){
        $sku_id = $request->sku_id;
        $from = ProductService::getFrom($request);
        if(!$sku_id) return $this->error("参数sku_id缺失");
        $pService =  new ProductService();
        $pid = $pService->getSkuSpuMapFromCacheBySkuId($sku_id);
        if(!$pid) return $this->error(0,"未找寻到对应商品ID");
        $product = $pService->getProductInfoFromCache($pid,[],$from);
        $skus = $product['skus'];
        $sku = [];
        foreach($skus as $one){
            if($one['sku_id'] == $sku_id) {
                $sku = $one;
                break;
            }
        }
        if(!$sku) return $this->error(0,"未找寻到对应Sku");
        $prodcut['sku'] = $sku;
        $this->success($product);
    }

    //批量根据sku ID 查询商品信息（特殊处理:3,5-2,7   5-2这种是套装，不是sku id）
    public function getProductInfoBySkuIds(Request $request){
        $sku_ids = $request->sku_ids;
        $from = ProductService::getFrom($request);
        if(!$sku_ids) return $this->error(0,"参数sku_ids缺失");
        $sku_ids = explode(',',$sku_ids);
        $sku_ids = array_filter($sku_ids);

        //真实sku ids
        $true_sku_ids = [];
        foreach($sku_ids as $sku_id){
            list($sid,$type) = ProductService::parsePid($sku_id);
            if($type != 2) $true_sku_ids[] = $sid;
        }
        $pService =  new ProductService();
        $pids = $pService->getSkuSpuMapFromCacheBySkuId($true_sku_ids);
        if(!$pids && (count($true_sku_ids) == count($sku_ids))) return $this->error(0,"未找寻到对应商品ID");

        $ret = [];
        foreach($sku_ids as $sku_id){
            list($sid,$type) = ProductService::parsePid($sku_id);
            $product = [];
            if($type == 2){
                $product = $pService->getColletionInfoFromCache($sid,[],$from);
            }elseif($pid = $pids[$sid]){
                $product = $pService->getProductInfoFromCache($pid,[],$from);
                if($product) $product['search_sku_id'] = $sku_id;
                $skus = $product['skus'];
                $sku = [];
                foreach($skus as $one){
                    if($one['sku_id'] == $sku_id) {
                        $sku = $one;
                        break;
                    }
                }
                if(!$sku) continue;
                $product['sku'] = $sku;
            }
            //输出最少字段
            if($request->get('type')=='mini'){
                $stock = intval(array_get($product,'sku.stock')?:0);
                $product = [
                    'id'=>array_get($product,'id')?:'',
                    'product_name'=>array_get($product,'product_name')?:'',
                    'product_desc'=>array_get($product,'product_desc')?:'',
                    'sku'=>['spec_desc'=>array_get($product,'sku.spec_desc')?:''],
                    'default_ori_price'=>array_get($product,'default_ori_price')?:'',
                    'ori_price'=>array_get($product,'sku.ori_price')?:'',
                    'stock'=>$stock>0?$stock:0,
                ];
            }
            if($product) $ret[$sku_id] = $product;
        }

        return $this->success($ret);
    }





    /**
     * 获取分类列表.
     */
    public function getCategoryTree(Request $request)
    {
        $cService = new CategoryService();
        $tree = $cService->cacheCategoryTree();
        return $this->success($tree);
    }


    public function getCatInfo(Request $request){
        $id = $request->id;
        $sub = $request->sub??0;
        if(!$id) return $this->error('参数缺失');
        $pService = new ProductService();
        $info = $pService->getCatInfoFromCache($id,$sub);
//        $data = [
//            'product_num'=>9,
//            'cat_name'=>'东方之夜',
//            'cat_nameen'=>'yyyyy',
//            'cat_desc'=>'东方之夜',
//            'cat_kv_image'=>'http://xxxx',
//            'share_image'=>'http://xxxx',
//            'share_content'=>'分享内容',
//        ];
        return $this->success($info);
    }

    /**
     * 为您推荐
    '1' => '用户最近浏览的前6个产品',
    '7' => '默认Sort升序',
    '8' => '默认Sort降序',
    '9' => '自定义商品',
     */
    public function recommend(Request $request)
    {
        $pid = $request->id;
        $flag = $request->flag??'';
        $page = $request->page??1;
        $page_size = 6;
        $request['simple'] = 1;
        $from = ProductService::getFrom($request);

        if(!in_array($flag,ProductService::$recommendConfigDBName)) return $this->error(0,'推荐类型不正确');
        $pService = new ProductService();
        $recommendConfig = $pService->getRecInfoFromCache($flag);
        list($id,$type) = ProductService::parsePid($pid);
        $info = ($type == 2)?$pService->getColletionInfoFromCache($id):$pService->getProductInfoFromCache($id);
        //重写pdp_rec 读取商品中的推荐配置而非全局的配置
        if($flag=='pdp_rec' && !empty($info['rec_spu'])){
            $recommendConfig['config_value'] = 9;
            $rec_spu = $info['rec_spu'];
            $rec_spu_arr = explode(',',$rec_spu);
            $redis = ProductService::getRedis();
            $rec_spu = $redis->hmget(config('app.name').':spucode:map',$rec_spu_arr);
            $rec_spu = array_filter($rec_spu);
            if($rec_spu){
                $recommendConfig['extension']['product_id'] = implode(',',$rec_spu);

            }
        }

        if(!empty($info['rec_cat_id'])){
            $recommendConfig['config_value'] = 6;
            $recommendConfig['extension']['cat_id'] = $info['rec_cat_id'];
        }
        if (false === $recommendConfig) {
            return $this->success(['list' => []]);
        }
        $prodIds = [];
        switch ($recommendConfig['config_value']) {
            case '1':
                //壮壮提供足迹接口
                $http = new Http();
                //获取商品信息
                $recentBack = $http->curl('footprint/getPagePids', ['page' => $page,'page_size'=>$page_size]);
                if (1 === $recentBack['code'] && isset($recentBack['data']['ids'])) {
                    $prodIds = array_slice($recentBack['data']['ids'],0,$page_size);
                }
                break;
            case '4':
                //收藏夹接口
                $http = new Http();
                $recentBack = $http->curl('fav/getPagePids',  ['page' => $page,'page_size'=>$page_size]);
                if (1 === $recentBack['code'] && isset($recentBack['data']['ids'])) {
                    $prodIds = $recentBack['data']['ids'];
                }
                break;
            case '5':
                $pBack = $pService->getLatestPids($page_size);
                if (!empty($pBack)) {
                    $prodIds = $pBack;
                }
                break;
            case '6':
                $cat_pids = [];
                if($cat_id = $recommendConfig['extension']['cat_id']??0){
                    $cat_pids = $pService->getCatProdIdListFromCache($cat_id);
                }
                if (!empty($cat_pids)) {
                    $prodIds = array_slice($cat_pids,0,$page_size);
                }
                break;
            case '7':
                $redis = ProductService::getRedis();
                $asc_key = config('app.name').':recommend:sort_asc';
                $ids_asc = $redis->get($asc_key);
                if($ids_asc){
                    $prodIds = json_decode($ids_asc,true);
                }
                break;
            case '8':
                $redis = ProductService::getRedis();
                $desc_key = config('app.name').':recommend:sort_desc';
                $ids_desc = $redis->get($desc_key);
                if($ids_desc){
                    $prodIds = json_decode($ids_desc,true);
                }
                break;
            case '9':
                $product_id_str = array_get($recommendConfig,'extension.product_id');
                if($product_id_str){
                    $prodIds = array_reduce(explode(',',$product_id_str),function($result,$item){
                        $result[] = ['id'=>$item,'product_type'=>1];
                        return $result;
                    },[]);
                }
                break;
        }
        $list = [];
        if (!empty($prodIds)) {
            foreach($prodIds as $id){
                $pid = $id['id'];
                $type = $id['product_type']??1;
                if($type == 2)
                    $product = $pService->getColletionInfoFromCache($pid,[],$from);
                else
                    $product = $pService->getProductInfoFromCache($pid,[],$from);
                if(!ProductService::checkProductLegal($product)) continue;
                if($product){
                    $list[] = $product;
                }
            }
        }
        $return['list'] = $list;

        return $this->success($return);
    }

    public function addSalesVolume(Request $request){
        //{"params":{"skutest001":1,"skutest002":2,"skutest0011":2}}
        $params = $request->get('params');
        (new ProductService())->addSalesVolume($params);
        return $this->success([]);
    }

}
