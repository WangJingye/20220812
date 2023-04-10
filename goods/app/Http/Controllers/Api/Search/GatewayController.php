<?php

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Api\Controller;
use App\Model\Goods\Category;
use App\Model\Help;
use App\Service\Goods\ProductService;
use App\Service\Goods\SearchService;
use Illuminate\Http\Request;
use App\Model\Search\Product;
use App\Model\Search\BlackList;
use App\Model\Search\Redirect;
use App\Model\Search\Catalog;
use Illuminate\Support\Facades\Redis;
use \App\Model\Redis as RedisM;
use Illuminate\Support\Facades\Log;

class GatewayController extends Controller
{
    public function searchProduct(Request $request)
    {
        $message = '对不起，您搜索的关键词无匹配结果~';
        try {
            $keywords = $request->get('keyword');
            $no_result["list"] = [];
            $no_result["totalPage"]  = "1";
            $no_result["count"]  = 0;
            $no_result["curPage"]    = "1";
            $no_result["type"]    = "search";
            if($keywords){
                //①首先进行黑名单校验，如果是在黑名单里直接提示无法匹配。
                $isBlack = SearchService::matchBlackList($keywords);
                if ($isBlack) {
                    return $this->success($no_result,$message);
                }
                SearchService::convertSynonym($keywords);
                $request->offsetSet('keyword',$keywords);
            }
            $params = $request->all();
            Log::info('es_search_request',$params);
            //④剩余的场景用多条件并行在ES中搜素
            list($count,$list,$filter) = Product::matchProduct($params);
            $input_cat_id = $request->get('cat_id');
            $pService = new ProductService();
            if($input_cat_id){
                $id_infos = $pService->getCatProdIdListFromCache($input_cat_id);
                $ids = array_column($id_infos,'id');
                $page = $request->get('page')?:1;
                $start=($page-1)*10;
                $ids = array_slice($ids,$start,10);
            }else{
                $ids = array_column($list,'unique_id');
            }
            $products = $pService->getProductInfoByUniqueIds($ids,true,1);
            $products = array_values($products);
            //获取分类页的分类名称(优先用筛选里的分类名称)
            $cat_id = array_get($params,'filter.cat')?:($request->get('cat_id')?:0);
            $cat = Category::getCachedCatDetailById($cat_id);
            $cat_name = array_get($cat,'cat_name');
            $cat_image = array_get($cat,'cat_kv_image');
            $cat_desc = array_get($cat,'cat_desc');
            if ($products) {
                SearchService::setHotKeyword($keywords);
                $ret['curPage'] = $request->page??1;
                $ret['totalPage'] = ceil($count/10);
                $ret['count'] = $count;
                $ret['list'] = $products;
                $ret['type'] = 'search';    //search 搜索列表  product 直跳商品 cat 直跳类目 project 直跳专题页
                $ret['cat_name'] = $cat_name;
                $ret['cat_image'] = $cat_image;
                $ret['cat_desc'] = $cat_desc;
                //start
                $ret['filter']['cat'] = $filter['cat'];
                $ret['filter']['sub_cat'] = $filter['sub_cat'];
                $ret['sub_cat_name'] = $filter['sub_cat_name'];
                $ret['filter']['capacity_g'] = $filter['capacity_g'];
                $ret['capacity_g_name'] = $filter['capacity_g_name'];
                $ret['filter']['capacity_ml'] = $filter['capacity_ml'];
                $ret['filter']['option_price'] = $filter['option_price'];
                //end
                return $this->success($ret);
            } else {
                $no_result['cat_name'] = $cat_name;
                $no_result['cat_image'] = $cat_image;
                $no_result['cat_desc'] = $cat_desc;
                //start
                $no_result['filter']['cat'] = $filter['cat'];
                $no_result['filter']['sub_cat'] = $filter['sub_cat'];
                $no_result['sub_cat_name'] = $filter['sub_cat_name'];
                $no_result['filter']['capacity_g'] = $filter['capacity_g'];
                $no_result['capacity_g_name'] = $filter['capacity_g_name'];
                $no_result['filter']['capacity_ml'] = $filter['capacity_ml'];
                $no_result['filter']['option_price'] = $filter['option_price'];
                //end
                return $this->success($no_result,$message);
            }
        } catch (\Exception $exception) {
            return $this->error(0,"网络异常，请重试！",$exception->getMessage());
        }
    }

    public function getFilter(Request $request)
    {
        try {
            $keywords = $request->get('keyword');
            $cat_id = $request->get('cat_id')?:0;
            $filter['cat'] = Product::getCatsFilter($cat_id);
            if($keywords){
                //①首先进行黑名单校验，如果是在黑名单里直接提示无法匹配。
                $isBlack = SearchService::matchBlackList($keywords);
                if ($isBlack) {
                    return $this->success(compact('filter'),'ok');
                }
                SearchService::convertSynonym($keywords);
                $request->offsetSet('keyword',$keywords);
            }
            list($filter['capacity_g'],$filter['capacity_ml']) = Product::getFilter($keywords,$cat_id);
            return $this->success(compact('filter'),'ok');
            //获取分类页的分类名称
        } catch (\Exception $exception) {
            return $this->error(0,"网络异常，请重试！",$exception->getMessage());
        }
    }

    public function searchCatalog(Request $request){
        $message = '对不起，您搜索的关键词无匹配结果~';
        $no_result["list"] = [];
        $no_result["totalPage"]  = "0";

        try{
            $result = Catalog::matchCatalog($request->all());
            if ($result) {
                $categoryId = $request->get("categoryId");
                $Statistics_Redis = Redis::connection('statistics');
                $enCateLevelInfo = $Statistics_Redis->HGET(config('redis.cateLevelInfo'), $categoryId);
                $categoryDetail = json_decode($enCateLevelInfo,true);
                $result['name'] = $categoryDetail['category_name'];
                $result['banner'] = $categoryDetail['category_kv_image'];
                $result['share']['title'] = $categoryDetail['share_content'];
                $result['share']['image'] = $categoryDetail['share_image'];

                return $this->success($result);
            } else {
                return $this->success($no_result,$message);
            }
        }
        catch (\Exception $exception) {
            return $this->error(0,"网络异常，请重试！",$exception->getMessage());
        }

    }

    public function setBlackList(Request $request)
    {
        $blacklist = $request->get('keywords');
        $result = BlackList::setBlackList($blacklist);

        return $result;
    }


    public function proudctToEs(Request $request)
    {
        $product_info = $request->get("product");
        $result = Product::updateProductToES($product_info);

        return $this->success($result);
    }


    public function jmterToEs(Request $request)
    {
        $product_info = $request->getContent();
        $result = Product::updateProductToES($product_info);

        return $this->success($result);
    }

    public function updateProductToES(Request $request)
    {
        $proudct_info = Product::updateProductToES($request->products);

        return $proudct_info;
    }

    public function getHotKeywords(){
        djenejsnej:
        $ks = ['冰白面膜','豆腐霜','蛋白水','微滴精华','黑油','黑皂','眼膜','冻膜','泥膜'];
//        $h_ks = SearchService::getTopHotKeyword(10);
//        $h_ks = array_filter($h_ks);
//        $ks = array_merge($ks,$h_ks);
        $b_ks = SearchService::getBlackList()??[];

        $rets = array_diff($ks,$b_ks);
        $rets = array_slice($rets,0,10);
        $hot_key = [];
        foreach($rets as $ret){//过滤掉非中英文数字的非法字符
            preg_match_all("/[\w\x{4e00}-\x{9fa5}]+/u", $ret, $matches);
            $hot_key[] = join('', $matches[0]);
        }
        $hot_key = array_unique($hot_key);
        return $this->success($hot_key);
    }

}
