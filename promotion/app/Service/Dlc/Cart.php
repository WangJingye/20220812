<?php namespace App\Service\Dlc;

use App\Lib\Http;
use Illuminate\Support\Facades\Log;

class Cart
{
    /**
     * @param $skusStr
     * @param string $prefix
     * @return bool
     * @throws \Exception
     */
    public static function checkItems($skusStr,$prefix=''){
        $prefix = $prefix?:'不存在的SKU';
        $http = new Http();
        $input_params = ['sku_ids'=>$skusStr];
        $products = $http->curl('outward/product/getProductInfoBySkuIds',$input_params);
        $product_data = $products['data']??[];
        if($product_data){
            $skus = explode(',',$skusStr);
            $errorSkus = array_reduce($skus,function($errorSkus,$sku) use($product_data){
                if(!array_key_exists($sku,$product_data)){
                    array_unshift($errorSkus,$sku);
                }return $errorSkus;
            },[]);
            if($errorSkus){
                $errorSkusStr = implode(',',$errorSkus);
                throw new \Exception("{$prefix}:{$errorSkusStr}");
            }return true;
        }throw new \Exception("{$prefix}:{$skusStr}");
    }


}