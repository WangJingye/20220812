<?php namespace App\Service\Goods;

use App\Service\Goods\ProductService;
use App\Console\Commands\Product as ProductCommand;

class CacheService
{
    /**
     * 刷新产品和分类
     * @return bool|string
     */
    public static function product_clear(){
        try {
            $cmd = new ProductCommand(0);
            $cmd->cacheAllProduct();
            $cmd->cacheCatProductIdList();
            $cmd->cacheAllSkuSpuMap();
            $cmd->cacheAllCats();
            $cmd->cacheProductSort();
            return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * 刷新广告
     * @return bool|string
     */
    public static function ad_clear(){
        try {
            $cmd = new ProductCommand(0);
            $cmd->cacheAllLocAds();
            return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * 刷新黑名单和同义词
     * @return bool|string
     */
    public static function synonym_clear(){
        try {
            $cmd = new ProductCommand(0);
            $cmd->cacheAllBlackList();
            $cmd->cacheAllSynonym();
            return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public static function rec_clear(){
        try {
            $cmd = new ProductCommand(0);
            $cmd->cacheAllRec();
            return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }


}
