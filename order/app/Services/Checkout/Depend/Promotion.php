<?php namespace App\Services\Checkout\Depend;


class Promotion
{
    public $promotion_api = 'promotion/cart/applyNew';
    public function getPromotionInfo($data_obj){
        //将地址信息转换为省用于给促销接口算运费
        $data_obj = \App\Services\Dlc\Checkout::convertAddress2Province($data_obj);
        /** @var \App\Services\Checkout\Data\Data $data_obj */
        $data = $data_obj->getData();
        $api = app('ApiRequestInner',['module'=>'promotion']);
        $params = $this->composePromotionParams($data);
        $resp = $api->request($this->promotion_api,'POST',$params);
        $flag = $this->validateFreeTry($data,$resp);
        if(!$flag){//试用装没有了
            return false;
        }
        $flag = $this->validateFreeTryLimitedQty($data,$resp);
        if(!$flag){//选择试用装数量大于允许数量
            return false;
        }
        $resp = $this->formatCartItems($resp);
        $data_obj = $data_obj->setPromotionData($resp,$params)
                                ->setPromotionInfo()
                                ->replaceByPromotionCoupon()
                                ->convertCouponId();
        return $data_obj;
    }

    //把每个cartItems都加上car_item_id作为Key
    private function formatCartItems($promotion_data){
        $cart_items = $promotion_data['cartItems'];
        $new_items = [];
        foreach($cart_items as $item){
            $cart_item_id = $item['cart_item_id'];
            $new_items[$cart_item_id] = $item;
        }
        $promotion_data['cartItems'] = $new_items;
        return $promotion_data;
    }

    //组装请求促销接口的入参
    public function composePromotionParams($data){
        $channel = $data['channel'];
        $coupon_id = $data['coupon_id'];
        $code = $data['coupon_code'];
        $total_points = $data['member_info']['total_points'];
        $member_coupon_list = (array) $data['member_info']['coupon_list']??[];
        $input_member_coupon_list = [];
        foreach($member_coupon_list as $item){
            $input_member_coupon_list[] = $item['coupon_id'];
        }
        $used_points = $data['used_points'];
        $cartItems = [];
        $goods = $data['goods_list']['goods'];
        foreach($goods as $good){
            $main = $good['main'];
            foreach($main as $item){
                $cartItems[] = [
                    'cart_item_id'=>$item['cart_item_id'],
                    'sku'=>$item['sku'],
                    'qty'=>$item['qty'],
                    'mid'=>$item['cats'],//系列
                    'styleNumber'=>$item['product_id'],//spu
                    'priceType'=>'X',
                    'price'=>bcmul($item['original_price'],$item['qty']),
                    'unit_price'=>$item['original_price'],
                    'labourPrice'=>0,
                    'pro_type'=>'auto',
                    'usedPoint'=>0,
                    'discount'=>0,
                    'maxUsedPoint'=>0,
                    'product_type'=>$item['product_type'],//只是为了传递
                    'products'=>$item['products'],//只是为了传递变量
                ];
            }
        }
        $coupon_id = $this->validateInputCouponId($coupon_id,$input_member_coupon_list);
        $params = [
            'coupon_id'=>$coupon_id,
            'member_coupon_list'=>implode(',',$input_member_coupon_list),
            'code'=>$code,
            'total_points'=>0,//$total_points,
            'used_points'=>0,//$used_points,
            'page'=>'order',
            'cartItems'=>$cartItems,
            'from'=>$channel,
        ];
        if(!empty($data['address'])){
            $params['address'] = $data['address'];
        }
        return $params;
    }

    //验证用户输入的coupon_id是在用户自己的优惠券列表里面的
    private function validateInputCouponId($coupon_id,$member_coupon_list){
        if(!in_array($coupon_id,$member_coupon_list)){
            return '';
        }
        return $coupon_id;
    }

    //验证可以选择的试用装数量
    private function validateFreeTryLimitedQty($data,$promotion_data){
        $input_free_try = $data['goods_list']['free_try']??[];
        $promotion_freetry_limited_qty = $promotion_data['order_freetry_limited_qty']??0;
        $input_free_try_count = count($input_free_try);
        if($input_free_try_count > $promotion_freetry_limited_qty){//选择的数量大于允许的数量
            return false;
        }
        return true;
    }

    //如果再次运行促销的时候跟当时提交的试用装不同，报错
    //如果当时选择的试用装，已经不在促销返回里面
    public function validateFreeTry($data,$promotion_data){
        $input_free_try = $data['goods_list']['free_try'];
        $promotion_return_free_try = $promotion_data['order_freetry']??[];
        if(!$input_free_try){//没有试用装
            return true;
        }
        if(!$promotion_return_free_try){//促销返回没有试用装
            return false;
        }
        $promotion_return_free_try_skus = [];
        foreach($promotion_return_free_try as $item){
            $skus = $item['gift_skus'];
            $skus_arr = explode(',',$skus);
            foreach($skus_arr as $sku){
                $promotion_return_free_try_skus[] = $sku;
            }
        }
        foreach ($input_free_try as $item){
            $sku = $item['sku'];
            if(!in_array($sku,$promotion_return_free_try_skus)){
                return false;
            }
        }
        return true;
    }

}
