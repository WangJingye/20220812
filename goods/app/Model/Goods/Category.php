<?php

namespace App\Model\Goods;
use App\Model\Common\YouShu;
use App\Service\Goods\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Category extends Model
{
    //指定表名
    protected $table = 'tb_category';
    protected $relateTable = 'tb_prod_cat_relation';
    protected $guarded = [];

    protected $appends = ['cat_type_name'];

    public function getCatTypeNameAttribute(){
        return $this->cat_type==1?'常规类目':'活动类目';
    }

    const YOUSHU_API = [
        1 => 'https://test.zhls.qq.com/data-api/v1/product_categories/add'
    ];

    /**
     * 获得此分类的产品
     */
    public function spus()
    {
        return $this->belongsToMany('App\Model\Goods\Spu', $this->relateTable, 'cat_id', 'product_idx');
    }

//    public static function getCatProductsById($id){
//        $records = Category::find($id)->spus->get();
//        return $records->isEmpty()?[]:$records->toArray();
//    }

    public static function updateById($id,$upData){
        $upSum = Category::where('id',$id)->update($upData);
        return $upSum;
    }

    //根据parent cat ids 批量获取类目信息
    public static function batchGetCatInfosByParentIds($parentIds = [0]){
        $records = Category::whereIn('parent_cat_id',$parentIds)->get();
//        if($records->isEmpty()){
//            return [];
//        }else{
//            $records = $records->toArray();
//        }
        return $records->isEmpty()?[]:$records->toArray();
    }

    public static function getParentsByCatId($cat_id,&$ret = []){
        if(!$cat_id) return [];
        $cat = Category::where('id',$cat_id)->first();
        if(!$cat) return [];
        $ret[] = [
            'id'=>$cat['id'],
            'cat_name'=>$cat['cat_name'],
        ];

        if($p_catid = $cat['parent_cat_id']) {
            Category::getParentsByCatId($p_catid,$ret);
        }
    }

    public static function getChildrenByCatIds($cat_ids,&$ret){
        if(!$cat_ids) return [];
        $cats = Category::whereIn('parent_cat_id',$cat_ids)->get();
        if($cats->isEmpty()) return [];
        $cats = object2Array($cats);
        foreach($cats as $cat){
            $ret[] = [
                'id'=>$cat['id'],
                'cat_name'=>$cat['cat_name'],
                'sort'=>$cat['sort'],
                'updated_at'=>$cat['updated_at'],
                'parent_cat_id'=>$cat['parent_cat_id'],
            ];
        }
        $ids = array_column($cats,'id');
        Category::getChildrenByCatIds($ids,$ret);
    }

    public static function batchOffCat($cat_ids){
        return Category::whereIn('id',$cat_ids)->update(['status'=>0]);
    }

    public static function upCat($cat_id){
        return Category::where('id',$cat_id)->update(['status'=>1]);
    }

    public static function getAllCat(){
        $all = Category::all();
        return $all?json_decode(json_encode($all),true):[];
    }

    //获取所有普通类目
    public static function getAllPrimaryCat(){
        $records = Category::where('cat_type',1)->where('status',1)->get();
        return $records->isEmpty()?[]:$records->toArray();
    }

    public static function getCatInfoById($id){
        $info = self::where('id',$id)->first();
        $cat_tree = [];
        self::getParentsByCatId($id,$cat_tree);
        $info['cat_tree'] = array_reverse($cat_tree);
        return $info?:[];
    }

    public static function getSimpleCatInfoById($id){
        $info = self::where('id',$id)->first();
        if(!$info) return [];
        return $info->toArray();
    }

    /**
     * 添加/更新商品类目
     */
    public static function taskProductCategories($id)
    {
        $sign = YouShu::getReqSign();
        //查询category信息
        $category = DB::table('tb_category as category')
                        ->select('category.id as external_category_id', 'category.cat_name as category_name', 'category.parent_cat_id as external_parent_category_id')
                        ->where('category.id', $id)
                        ->first();
        $result = [];
        if (!empty($category)) {
            $param = json_encode([
                "dataSourceId"  => "10903",
                "categories"    => [
                    [
                        "external_category_id"          => (string) $category->external_category_id,
                        "category_name"                 => $category->category_name,
                        "category_type"                 => 1,
                        "category_level"                => empty($category->external_parent_category_id) ? 0 : 1,
                        "external_parent_category_id"   => (string) $category->external_parent_category_id,
                        "is_root"                       => empty($category->external_parent_category_id) ? true : false
                    ],
                ],
            ], true);
            Log::info('taskProductCategories1:'.$param);
            //请求有数api
            $url = self::YOUSHU_API[1] . '?' . $sign;
            $http = new YouShu();
            $result = $http->curl_post($url, $param);
            Log::info('taskProductCategories:'.json_encode($result, true));
        }
        return $result;
    }

    /**
     * 导入商品类目（有数）历史数据
     */
    public static function exportCategoriesHistory()
    {
        $sign = YouShu::getReqSign();
        //查询category信息
        $categoryInfo = DB::table('tb_category as category')
                            ->select('category.id as external_category_id', 'category.cat_name as category_name', 'category.parent_cat_id as external_parent_category_id')
                            ->get();
        $result = [];
        if (!empty($categoryInfo)) {
            $categories = [];
            foreach ($categoryInfo as $k=>$category) {
                $categories[] = [
                    "external_category_id"          => (string) $category->external_category_id,
                    "category_name"                 => !empty($category->category_name) ? $category->category_name : '空',
                    "category_type"                 => 1,
                    "category_level"                => empty($category->external_parent_category_id) ? 0 : 1,
                    "external_parent_category_id"   => (string) $category->external_parent_category_id,
                    "is_root"                       => empty($category->external_parent_category_id) ? true : false
                ];   
            }
            $param = json_encode([
                "dataSourceId"  => "10903",
                "categories"    => $categories,
            ], true);
            //请求有数api
            $url = self::YOUSHU_API[1] . '?' . $sign;
            $http = new YouShu();
            $result = $http->curl_post($url, $param);
            Log::info('exportCategoriesHistory:'.json_encode($result, true));
        }
        return $result;
    }

    /**
     * 获取所有分类的所有父级
     * @return array
     */
    public static function getMergedParents(){
        $datas = Category::query()->pluck('parent_cat_id','id')->toArray();
        $result = [];
        foreach($datas as $id=>$pid){
            self::getParent($id,$datas,$parents);
            if($parents){
                $result[$id] = $parents;
            }
            unset($parents);
        }
        return $result;
    }

    /**
     * 递归获取父级
     * @param $cat_id
     * @param $datas
     * @param array $parents
     */
    private static function getParent($cat_id,$datas,&$parents=[]){
        if($cat_id && array_key_exists($cat_id,$datas)){
            $pid = $datas[$cat_id];
            if(!empty($pid)){
                $parents[] = $pid;
                self::getParent($pid,$datas,$parents);
            }
        }
    }

    /**
     * @param $cat_id
     * @return array|false|string|null
     */
    public static function getCachedCatNameById($cat_id){
        $redis = ProductService::getRedis();
        $key = config('app.name').':cats:index';
        if(is_array($cat_id)){
            return array_combine($cat_id,$redis->hmget($key,$cat_id));
        }return $redis->hget($key,$cat_id);
    }

    public static function getCachedCatStatus(){
        $redis = ProductService::getRedis();
        $key = config('app.name').':cats:status';
        return $redis->hgetall($key);
    }

    public static function getCachedCatDetailById($cat_id){
        $redis = ProductService::getRedis();
        $key = config('app.name').':cats:detail';
        if(is_array($cat_id)){
            return array_combine($cat_id,array_reduce($redis->hmget($key,$cat_id),function($result,$item){
                $result[] = $item?json_decode($item,true):[];
                return $result;
            }));
        }
        $item = $redis->hget($key,$cat_id);
        return $item?json_decode($item,true):[];
    }

    /**
     * 获取当前分类下一级的所有分类 如果没有 则取所有兄弟
     * @param $cat_id
     * @param $must_sub(1 只取子集, 0 子集没有取同集)
     * @return array
     */
    public static function getCachedAllNextLevelChildrenById($cat_id,$must_sub=0){
        $redis = ProductService::getRedis();
        $children_key = config('app.name').':cats:children';
        $brother_key = config('app.name').':cats:brother';
        $data = $redis->hget($children_key,$cat_id);
        if(empty($data)){
            if($must_sub==0){//没有子集取同级
                $data = $redis->hget($brother_key,$cat_id);
            }
        }return $data?json_decode($data):[];
    }

}
