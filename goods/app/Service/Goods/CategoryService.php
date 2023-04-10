<?php

namespace App\Service\Goods;
use App\Model\Goods\Category;
use App\Service\ServiceCommon;
use Illuminate\Support\Facades\Redis;
use App\Model\Goods\Tree;

class CategoryService extends ServiceCommon
{
    //缓存中获取商品信息 （被动缓存）
    public function getCategoryTreeFromCache(){
        $redis = Redis::connection('goods');
        $key = config('app.name').':category:tree';
        if($tree = $redis->get($key)){
            $tree = json_decode($tree,true);
        }else{
            $cService = new CategoryService();
            $tree = $cService->cacheCategoryTree();
        }
        return $tree??[];
    }

    //缓存商品信息
    public function cacheCategoryTree(){
        $redis = Redis::connection('goods');
        $cats = Category::getAllPrimaryCat();
        if(empty($cats)) return false;
        $Tree = new Tree();
        $tree = $Tree->getTreeData($cats, 'level', 'cat_name', 'id', 'parent_cat_id');
        if($tree){
            $redis->set(config('app.name').':category:tree',json_encode($tree));
            return $tree;
        }
        return [];
    }


}
