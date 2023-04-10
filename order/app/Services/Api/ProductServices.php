<?php namespace App\Services\Api;

use App\Exceptions\ApiPlaintextException;

class ProductServices
{
    /**
     * 调用商品模块获取商品详情(包含套装)
     * @param $sku
     * @param $from
     * @return null
     * @throws \Exception
     */
    public function getProductBySku($sku,$from = ''){
        if(is_array($sku)){
            $sku = implode(',',$sku);
        }
        /** @var \App\Services\ApiRequest\Inner $api */
        $api = app('ApiRequestInner',['module'=>'goods']);
        return $api->request('outward/product/getProductInfoBySkuIds','POST',[
            'sku_ids'=>$sku,
            'from'=>$from,
        ]);
    }


}
