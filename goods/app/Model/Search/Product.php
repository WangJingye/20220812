<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/10/31
 * Time: 21:40.
 */

namespace App\Model\Search;

use App\Http\Helpers\Api\GoodsCurlLog;
use App\Model\Goods\Category;
use App\Model\Goods\ProductCat;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class Product extends Model
{

    /**
     * 商品从DB存入ES中，并将需要搜索的几个关键词统一为仅首字母大写
     * @param $product
     * @return mixed
     */
    public static function updateProductToES($product)
    {
        $product_info = json_decode($product, true);
        //小季传来raw_product_name，代表需要修改ES中商品名称
        if(isset($product_info["raw_product_name"])){
            $product_info["product_name"] = $product_info["raw_product_name"];
            unset($product_info["raw_product_name"]);
        }

        //将商品接口传过来的品牌、系列、子系列等中的英文字母全部变成大写再存入ES
        $product_info["sub_collection_name"] = strtoupper($product_info["sub_collection_name"]);
        $product_info["brand"] = strtoupper($product_info["brand"]);
        $product_info["series"] = strtoupper($product_info["series"]);
        $product_info["collection_name"] = strtoupper($product_info["collection_name"]);
        $product_info["product_name"] = strtoupper($product_info["product_name"]);
        $product_info["section"] = strtoupper($product_info["section"]);
        $product_info["style_number"] = strtoupper($product_info["style_number"]);
        $product_info["custom_keyword"] = strtoupper($product_info["custom_keyword"]);


        $result = app('es')->index([
            'id' => $product_info['product_id'],
            'index' => config('database.connections.elasticsearch.index'),
            'type' => '_doc', 'body' => $product_info, ]
        );

        // 只取出需要的字段
        return $result;
    }

    /**
     * 批量接口
     * 商品从DB存入ES中，并将需要搜索的几个关键词统一为仅首字母大写
     * @param $product
     * @return mixed
     */
    public static function updateProductsToES($products)
    {
        $products = is_array($products)?$products:json_decode($products, true);
        $index = SearchES::getIndexFromBrandCode();

        $params['index'] = $index;
        $params['type'] = '_doc';
        $cat_names = [];

        //获取所有分类
        $categoryMergedParents = Category::getMergedParents();
        //获取所有产品和分类的对应关系
        $productCat = array_reduce(ProductCat::query()->select('id','cat_id','product_idx')->get()->toArray(),function($result,$item) use ($categoryMergedParents){
            $parents = array_get($categoryMergedParents,$item['cat_id']);
            $result[$item['product_idx']][$item['cat_id']] = $item['cat_id'];
            if($parents){
                foreach($parents as $parent){
                    $result[$item['product_idx']][$parent] = $parent;
                }
            }return $result;
        },[]);

        foreach($products as $product_info){
            $params['body'][] = ['index'=>['_id' => $product_info['unique_id'],'_index' => $index,'_type' => '_doc' ]];
            $es = [];
            $p_cat_names = [];

            //小季传来raw_product_name，代表需要修改ES中商品名称
            if(isset($product_info["raw_product_name"])){
                $product_info["product_name"] = $product_info["raw_product_name"];
                unset($product_info["raw_product_name"]);
            }

            $product_info['skus'] = empty($product_info['skus'])?[]:$product_info['skus'];
            if($product_info['product_type'] == 2){
                $prods = $product_info['products']??[];
                foreach ($prods as $prod){
                    if(empty($prod['skus'])) continue;
                    $product_info['skus'] = array_merge($product_info['skus'],$prod['skus']);
                }
            }

            //将商品接口传过来的品牌、系列、子系列等中的英文字母全部变成大写再存入ES
            $es["short_product_desc"] = strtoupper($product_info["short_product_desc"]);
            $es["product_desc"] = strtoupper($product_info["product_desc"]);
            $es["display_type"] = strtoupper($product_info["display_type"]);
            $es["product_id"] = strtoupper($product_info["product_id"]??'');
            $es["product_name"] = strtoupper($product_info["product_name"]);
            $es["product_name_en"] = strtoupper($product_info["product_name_en"]);
            $es["list_name"] = strtoupper($product_info["list_name"]);
            $es["custom_keyword"] = strtoupper($product_info["custom_keyword"]);
            $es["priority_cat_id"] = $product_info["priority_cat_id"];
            $es["product_type"] = $product_info["product_type"];
            $es["display_type"] = $product_info["display_type"];
            $es["lowest_ori_price"] = $product_info["lowest_ori_price"];
            $es["lowest_price"] = $product_info["lowest_price"];
            $es["status"] = (!empty($product_info['can_search']) && ($product_info["status"] == 1))?1:0;
            $es["highest_ori_price"] = $product_info["highest_ori_price"];
            $es["highest_price"] = $product_info["highest_price"];
            $es["unique_id"] = $product_info["unique_id"];
            $es["tag"] = $product_info["tag"]??'';
            $es["product_detail_wechat"] = is_array($product_info["product_detail_wechat"])?json_encode($product_info["product_detail_wechat"]):'';
            $es["product_detail_pc"] = is_array($product_info["product_detail_pc"])?json_encode($product_info["product_detail_pc"]):'';
            $es["products"] = json_encode($product_info['products']);
            $es["skus"] = json_encode($product_info['skus']);
            $es["tag"] = empty($product_info["tag"])?strtoupper($product_info["product_name"]):strtoupper($product_info["product_name"]).','.strtoupper($product_info["tag"]);
            $es["updated_at"] = $product_info['updated_at'];
            $es["is_gift_box"] = $product_info['is_gift_box'];
            $es["sort"] = $product_info['sort'];
            $es["sales"] = $product_info['sales'];//销量
            $es["list_price"] = $product_info["list_price"];

            $capacity_g = $capacity_ml = [];
            if($product_info['product_type']==1){
                //普通商品加入sku的规格
                $spec_types = $product_info['spec_type'];
                if($spec_types){
                    foreach($spec_types as $spec_type){
                        $skus = array_get($product_info,'skus');
                        foreach($skus as $sku){
                            $spec_property = array_get($sku,$spec_type)?:0;
                            $spec_value = array_get($sku,$spec_type.'_desc');
                            if($spec_value){
                                if($spec_type=='capacity_g'){
                                    $capacity_g[] = [
                                        'key'=>intval($spec_property),
                                        'value'=>$spec_value,
                                    ];
                                }elseif($spec_type=='capacity_ml'){
                                    $capacity_ml[] = [
                                        'key'=>intval($spec_property),
                                        'value'=>$spec_value,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            $es["specs_capacity_g"] = $capacity_g;
            $es["specs_capacity_ml"] = $capacity_ml;
            //包含的所有价格
            $skus = array_get($product_info,'skus');
            $all_price = [];
            foreach($skus as $sku){
                $all_price[] = [
                    'value'=>array_get($sku,'price')?:0
                ];
            }
            $es['all_price'] = $all_price;
            //获取该产品的分类和所有父级的分类
            $cat_all_arr = array_get($productCat,$product_info['id']);
            $es['cats_all'] = $cat_all_arr?array_reduce($cat_all_arr,function($result,$item){
                if($item){
                    $result[] = ['cat_id'=>$item];
                }
                return $result;
            },[]):[];

            //三级类目名称也作为商品的关键字 可以查询
            if(isset($product_info['cats'])){
                $es["cats"] = implode(',',$product_info['cats']);
                foreach($product_info['cats'] as $cat_id){
                    if(!isset($cat_names[$cat_id])){
                        $children = [];
                        Category::getChildrenByCatIds([$cat_id],$children);
                        if($children) {
                            $cat_names[$cat_id] = '';
                        } else {
                            $info = Category::getSimpleCatInfoById($cat_id);
                            $cat_names[$cat_id] = $info['cat_name']??'';
                            if(!empty($info['cat_name'])) $p_cat_names[] = $info['cat_name'];
                        }
                    }else{
                        if(!empty($cat_names[$cat_id])) $p_cat_names[] = $cat_names[$cat_id];
                    }
                }
            }
            $es['cat_names'] = empty($p_cat_names)?'':implode(',',$p_cat_names);
            if(isset($product_info['skus'])) $es["sku_ids"] = implode(',',array_column($product_info['skus'],'sku_id'));
            $params['body'][] = $es;
        }
        $result = app('es')->bulk($params);

        // 只取出需要的字段
        return $result;
    }


    /**
     * 通过关键词去ES中获取命中产品数据并清洗成商品ID列表
     * @param $request_info
     * @return array|bool
     */
    public static function matchProduct($request_info)
    {
        //获取聚合用于筛选
        $cat_id = array_get($request_info,'cat_id')?:0;
        //获取聚合(聚合根据keyword和cat_id 所以这里会多调用一次es)
        $matchResult = SearchES::SearchProductFromESByFilter($request_info);
        //组装规格
        $aggs_capacity_g = array_get($matchResult,'aggregations.specs_capacity_g_name.spec_name.buckets');
        //capacity_g显示所有
        self::capacity_g_all($aggs_capacity_g);
        if(count($aggs_capacity_g)){
            $capacity_g_checked = array_filter(explode(',',array_get($request_info,'filter.capacity_g')?:''));
            $aggs_capacity_g = array_reduce($aggs_capacity_g,function($result,$item) use($capacity_g_checked){
                $active = in_array($item['key'],$capacity_g_checked)?true:false;
                $result[] = [
                    'name'=>$item['key'],
                    'val'=>$item['key'],
                    'active'=>$active,
                ];
                return $result;
            },[]);
        }
        $aggs_capacity_ml = array_get($matchResult,'aggregations.specs_capacity_ml_name.spec_name.buckets');
        if(count($aggs_capacity_ml)){
            $capacity_ml_checked = array_filter(explode(',',array_get($request_info,'filter.capacity_ml')?:''));
            $aggs_capacity_ml = array_reduce($aggs_capacity_ml,function($result,$item) use(&$capacity_ml_sort,$capacity_ml_checked){
                $active = in_array($item['key'],$capacity_ml_checked)?true:false;
                $result[] = [
                    'name'=>$item['key'],
                    'val'=>$item['key'],
                    'active'=>$active,
                ];
                return $result;
            },[]);
        }
        //只有通过全部和搜索进来的才会显示一级分类
        if(empty($cat_id)){
            //组装聚合后的筛选
            $filter_cat_id = array_get($request_info,'filter.cat');
            $filter_cats = self::getCatsFilter($cat_id,$filter_cat_id);
        }else{
            $filter_cat_id = $cat_id;
        }
        //二级分类
        if(!empty($filter_cat_id)){
            $filter_sub_cat_id = array_get($request_info,'filter.sub_cat');
            $filter_sub_cats = self::getCatsFilter($filter_cat_id,$filter_sub_cat_id,1);
            $sub_cat_name = Category::getCachedCatNameById($filter_cat_id);
            //hardcode处理start
            //更改筛选标题
            $filter_sub_cat_name = in_array($sub_cat_name,['身体护理','男士护理'])?'产品':'系列';
            if($sub_cat_name=='室内香氛系列'){
                //该系列不显示毫升规格
                $aggs_capacity_ml = [];
            }
            //hardcode end
        }
        //hardcode处理start
        if(empty($cat_id)&&empty($filter_cat_id)){//没有分类的时候不显示香调
            $aggs_capacity_g=[];
        }
        //hardcode end
        //价格区间筛选(不做聚合)
        $option_price = config('common.option_price');
        $option_price_checked = array_filter(explode(',',array_get($request_info,'filter.option_price')?:''));
        $option_price = array_reduce($option_price,function($result,$item) use($option_price_checked){
            $active = in_array($item['key'],$option_price_checked)?true:false;
            $result[] = [
                'name'=>$item['name'],
                'val'=>$item['key'],
                'active'=>$active,
            ];
            return $result;
        });
        $filter = [
            'cat'=>$filter_cats??[],
            'sub_cat'=>$filter_sub_cats??[],
            'sub_cat_name'=>$filter_sub_cat_name??'',
            'capacity_g'=>$aggs_capacity_g,
            'capacity_g_name'=>'香调',
            'capacity_ml'=>$aggs_capacity_ml,
//            'option_price'=>$option_price,
            'option_price'=>[],
        ];

        $recs = $matchResult['hits']['hits'];
        $count = $matchResult['hits']['total']['value'];
        return [$count,array_column($recs,'_source'),$filter] ;
    }

    public static function getCatsFilter($cat_id,$filter_cat_id = 0,$must_sub = 0){
        //获取下一级的所有分类
        $categoryChildren = Category::getCachedAllNextLevelChildrenById($cat_id,$must_sub);
        if($categoryChildren){
            $categoryChildren = Category::getCachedCatNameById($categoryChildren);
            $cat_status = Category::getCachedCatStatus();
            foreach($categoryChildren as $k=>$v){
                $active = ($k==$filter_cat_id)?true:false;
                $categoryChildren[$k] = [
                    'name'=>$v,
                    'val'=>$k,
                    'active'=>$active,
                ];
                if(array_get($cat_status,$k)!=1){
                    unset($categoryChildren[$k]);
                }
            }
        }
        return array_values($categoryChildren);
    }

    public static function getFilter($keyword,$cat_id)
    {
        //获取聚合(聚合根据keyword和cat_id 所以这里会多调用一次es)
        $matchResult = SearchES::SearchProductFromESByFilter(compact('keyword','cat_id'),1);
        //组装规格
        $aggs_capacity_g = array_get($matchResult,'aggregations.specs_capacity_g_name.spec_name.buckets');
        if(count($aggs_capacity_g)){
            $aggs_capacity_g = array_reduce($aggs_capacity_g,function($result,$item){
                $result[] = [
                    'name'=>$item['key'],
                    'val'=>$item['key'],
                ];
                return $result;
            },[]);
        }
        $aggs_capacity_ml = array_get($matchResult,'aggregations.specs_capacity_ml_name.spec_name.buckets');
        if(count($aggs_capacity_ml)){
            $capacity_ml_sort = [];
            $aggs_capacity_ml = array_reduce($aggs_capacity_ml,function($result,$item) use(&$capacity_ml_sort){
                $result[] = [
                    'name'=>$item['key'],
                    'val'=>$item['key'],
                ];
                $capacity_ml_sort[] = (int)str_replace('ml','',$item['key']);
                return $result;
            },[]);
            //聚合后排序(规格 毫升)
            array_multisort($capacity_ml_sort,SORT_ASC,$aggs_capacity_ml);
        }
        //组装聚合后的筛选
        return [$aggs_capacity_g,$aggs_capacity_ml];
    }

    /**
     * 通过post方式CURL调用内部接口
     * @param $url
     * @param $data_string
     * @return array
     */
    public static function http_post_data($url, $data_string)
    {
        $header = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: '.strlen($data_string));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $logger = new GoodsCurlLog();
        $logger->curlLog(json_decode($data_string, true),"POST" , $url,
                            $header, $return_code, json_encode($return_content) );
        return array($return_code, $return_content);
    }


    /**
     * 通过产品ID获取商品信息明细
     * @param $arr
     * @return mixed
     */
    public static function getProductById($arr)
    {
        $url = 'http://goods.css.com.cn/goods/inner/getSearchProdsByIds';
        //生产环境走内网，测试环境走UAT外网
        if(env("APP_ENV") == "local"){
            $url = "https://wecapiuat.chowsangsang.com.cn/pdt/goods/inner/getSearchProdsByIds";
        }
        $param['prodIdStr'] = json_encode($arr);
        $response = self::http_post_data($url, json_encode($param));
        $product_info = json_decode($response[1], true)['data'];
        return $product_info;
    }

    /**
     * 香调根据分类和keyword来显示 不根据价格或规格来显示
     * @param $aggs_capacity_g
     */
    public static function capacity_g_all(&$aggs_capacity_g){
        $request = request()->all();
        $params = [];
        $params['keyword'] = array_get($request,'keyword');
        $params['cat_id'] = array_get($request,'cat_id');
        $params['filter']['cat'] = array_get($request,'filter.cat');
        $params['filter']['sub_cat'] = array_get($request,'filter.sub_cat');
        $matchResult = SearchES::SearchProductFromESByFilter($params);
        $aggs_capacity_g = array_get($matchResult,'aggregations.specs_capacity_g_name.spec_name.buckets');
    }

}
