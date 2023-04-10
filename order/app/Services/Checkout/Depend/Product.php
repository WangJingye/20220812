<?php namespace App\Services\Checkout\Depend;

//实时获取商品价格和库存
class Product
{
    public $product_api = 'outward/product/getProductInfoBySkuIds';
    public function getProductInfo($data_obj){
        /** @var \App\Services\Checkout\Data\Data $data_obj */
        $data = $data_obj->getData();
        $channel = $data['channel'];
        $skus_arr = $this->getSkus($data);
        /** @var \App\Services\ApiRequest\Inner $api */
        $api = app('ApiRequestInner',['module'=>'goods']);
        $input_data = [
            'sku_ids'=>implode(',',$skus_arr),
            'from'=>$channel,
        ];
        $resp = $api->request($this->product_api,'POST',$input_data);
        $data_arr = $resp['data'];
        $product_data = $this->formatProductData($data_arr);
        $product_data = $this->validateProductStatus($product_data);
        if($product_data === false ){
            return false;
        }
        $data_obj = $data_obj->setProductData($product_data)->setProductsInfo();
        $data_obj = $this->validateMainProductPrice($data_obj);
        if($data_obj == false){
            return false;
        }
        try{

        }catch (\Exception $e){
            echo $e->getMessage();exit;
        }
        return $data_obj;
    }
    //付邮试用的获取商品信息，不验证0元商品价格
    public function getProductInfoForShipfeeTry($data_obj){
        /** @var \App\Services\Checkout\Data\Data $data_obj */
        $data = $data_obj->getData();
        $channel = $data['channel'];
        $skus_arr = $this->getSkus($data);
        /** @var \App\Services\ApiRequest\Inner $api */
        $api = app('ApiRequestInner',['module'=>'goods']);
        $input_data = [
            'sku_ids'=>implode(',',$skus_arr),
            'from'=>$channel,
        ];
        $resp = $api->request($this->product_api,'POST',$input_data);
        $data_arr = $resp['data'];
        $product_data = $this->formatProductData($data_arr);
        $product_data = $this->validateProductStatus($product_data);
        if($product_data === false ){
            return false;
        }
        $data_obj = $data_obj->setProductData($product_data)->setProductsInfo();
        return $data_obj;
    }

    //验证主商品价格不能为0
    private function validateMainProductPrice($data_obj){
        $data = $data_obj->getData();
        $goods = $data['goods_list']['goods']??[];
        foreach($goods as $good){
            $main = $good['main']??[];
            foreach($main as $item){
                $main_price = $item['original_price']??0;
                if($main_price == 0){
                    return false;
                }
            }
        }
        return $data_obj;
    }

    //验证下架商品不能下单
    private function validateProductStatus($product_data){
        foreach($product_data as $item){
            if(!$item['status']){
                return false;
            }
        }
        return $product_data;
    }

    //格式化商品接口返回的数据
    private function formatProductData($data_arr){
        $product_data = [];
        foreach($data_arr as $sku=>$item){
            $cats = $item['cats']??[];
            if(is_array($cats)){
                $cats = implode(',',$cats);
            }
            $product_data[$sku] = [
                'product_type'=>$item['product_type'],
                'product_desc'=>$item['product_desc'],
                'display_type'=>$item['display_type'],
                'cats'=>$cats,
                'spec_type'=>$item['spec_type']??'',
                'spec_desc'=>$item['sku']['spec_desc']??'',
                'spec_property'=>$item['sku']['spec_property']??'',
                'kv_images'=>$item['kv_images'],
                'products'=>$item['products'],
                'product_name'=>$item['product_name'],
                'ori_price'=>$item['sku']['ori_price']??'10000',
                'sku'=>$sku,
                'product_id'=>$item['unique_id']??'',
                'detail'=>$item,
                'revenue_type'=>$item['sku']['revenue_type']??'',
                'status'=>$item['status']??1,
                'include_skus'=>$item['sku']['include_skus'],
            ];
        }
        return $product_data;
    }

    private function getSkus($data){
        $goods = $data['goods_list']['goods'];
        $free_try = $data['goods_list']['free_try'];
        $skus = [];
        foreach($goods as $item){
            $main = $item['main'];
            foreach($main as $main_item){
                $skus[] = $main_item['sku'];
                if($main_item['product_type'] == '2'){
                    $products = $main_item['products'];
                    foreach($products as $suit_selected_sku){//套装skus
                        $skus[] = $suit_selected_sku;
                    }
                }
            }
            $gifts = $item['gifts'];
            foreach($gifts as $gift_item){
                $skus[] = $gift_item['sku'];
            }
        }
        //赠品，促销返回的赠品
        if(isset($data['promotion_data'])){
            $gifts = $data['promotion_data']['order_gift']??[];
            foreach($gifts as $item){
                $gift_arr = explode(',',$item['gift_skus']);
                foreach($gift_arr as $g){
                    $skus[] = $g;
                }
            }
            //实物券
            $product_coupon_sku = $data['product_coupon_sku'];
            foreach($product_coupon_sku as $item){
                $skus[] = $item['sku'];
            }
        }

        foreach($free_try as $item){
            $skus[] = $item['sku'];
        }
        return $skus;
    }

    /**
     * 组装追加打包的skus
     * @param $data_obj
     * @return mixed
     * @throws \Exception
     */
    public function setIncludeSkus($data_obj){
        /** @var \App\Services\Checkout\Data\Data $data_obj */
        $data_arr = $data_obj->getData();
        $include_skus = [];
        foreach($data_arr['goods_list']['goods'] as $item){
            foreach($item['main'] as $mainItem){
                if(!empty($mainItem['include_skus'])){
                    $include_skus = array_merge($include_skus,array_column($mainItem['include_skus'],'sku'));
                }
            }
        }
        if(!empty($include_skus)){
            $include_skus = array_unique($include_skus);
            /** @var \App\Services\ApiRequest\Inner $api */
            $api = app('ApiRequestInner',['module'=>'goods']);
            $input_data = [
                'sku_ids'=>implode(',',$include_skus),
                'from'=>$data_arr['channel'],
            ];
            $resp = $api->request('outward/product/getProductInfoBySkuIds','POST',$input_data);
            $resp_data = $resp['data'];
            $product_data = $this->formatProductData($resp_data);
            $data_obj = $data_obj->setIncludeSkusData($product_data);
        }
        return $data_obj;
    }

}
