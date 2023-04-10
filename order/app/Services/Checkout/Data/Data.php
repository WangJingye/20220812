<?php
namespace App\Services\Checkout\Data;

//下单的数据结构， 处理各个系统之间的不同数据映射关系
//下单需要保存起来的订单数据结构
//提供给前端的数据结构
use Illuminate\Support\Facades\Redis;

class Data
{
    public $data = [];
    private $suit_obj = false;
    /** @var \App\Services\Checkout\Promotion\Suit $promotion_suit_obj */
    public $promotion_suit_obj = false;

    public function initData(){
        $this->data['order_type'] = 1;//普通订单
        return $this;
    }

    public function setPromotionSuitObj($promotion_suit_obj){
        $this->promotion_suit_obj = $promotion_suit_obj;
        return $this;
    }

    public function getData(){
        return $this->data;
    }
    //获取使用积分
    public function getUsedPoints(){
        return $this->data['used_points'];
    }

    public function setCustomerId($customer_id){
        $this->data['customer_id'] = $customer_id;
        return $this;
    }

    public function setOpenId($openid){
        $this->data['openid'] = $openid;
        return $this;
    }

    //把用户选择下单结算的商品拿出来
    //在结算页面的选择也保存在这里
    public function setCheckoutItems($checkout_items){
        $checkout = $checkout_items['checkout'];
        $this->data['shipping_address_id'] = $checkout['shipping_address_id'];
        $this->data['used_points'] = $checkout['used_points'];
        $this->data['shipping_method'] = $checkout['shipping_method'];
        $this->data['payment_method'] = $checkout['payment_method'];
        $this->data['accepted_flag'] = $checkout['accepted_flag'];
        $this->data['guide'] = $checkout['guide'];//导购信息
        $this->data['invoice'] = $checkout['invoice'];
        $this->data['invoice']['type'] = $checkout['invoice']['type'];
        $this->data['invoice']['title'] = $checkout['invoice']['title'];
        $this->data['invoice']['id'] = $checkout['invoice']['id'];
        $this->data['invoice']['email'] = $checkout['invoice']['email']??'';
        $this->data['card'] = $checkout['card'];
        $this->data['card']['from'] = $checkout['card']['from'];
        $this->data['card']['to'] = $checkout['card']['to'];
        $this->data['card']['content'] = $checkout['card']['content'];
        $this->data['coupon_id'] = $checkout_items['coupon_id'];
        $this->data['coupon_code'] = $checkout_items['coupon_code'];
        //购物车选择下单的商品
        $goods = [
            [
                'main'=>$checkout_items['main'],//主商品
                'gifts'=>[],
            ],
        ];
        $goods_list = [
            'goods'=>$goods,
            'free_try'=>$checkout_items['free_try']??[],//试用装
        ];
        $this->data['goods_list'] = $goods_list;
        return $this;
    }

    //把用户输入的原始数据映射为系统的的数据结构
    public function mappingInput($input_data){
        $this->data['activityChannel'] = $input_data['activityChannel']??'';
        $this->data['entrance'] = $input_data['entrance']??'';
        $this->data['ltinfo'] = $input_data['ltinfo']??'';
        $this->data['channel'] = $input_data['channel'];//渠道
        $this->data['shipping_address_id'] = $input_data['shipping_address_id']??$this->data['shipping_address_id'];
        $this->data['coupon_id'] = $input_data['coupon_id']??$this->data['coupon_id'];
        $this->data['coupon_code'] = $input_data['coupon_code']??$this->data['coupon_code'];//优惠码
        $this->data['used_points'] = $input_data['used_points']??$this->data['used_points'];
        $this->data['shipping_method'] = $input_data['shipping_method']??$this->data['shipping_method'];
        $this->data['payment_method'] = $input_data['payment_method']??$this->data['payment_method'];
        $this->data['trade_type'] = $input_data['trade_type']??'';
        $this->data['accepted_flag'] = $input_data['accepted_flag']??$this->data['accepted_flag'];
        $this->data['guide'] = $input_data['guide']??$this->data['guide'];//导购信息
        $this->data['invoice'] = $input_data['invoice']??$this->data['invoice'];
        $this->data['invoice']['type'] = $input_data['invoice']['type']??'';
        $this->data['invoice']['title'] = $input_data['invoice']['title']??'';
        $this->data['invoice']['id'] = $input_data['invoice']['id']??'';
        $this->data['invoice']['email'] = $input_data['invoice']['email']??'';
        $this->data['card'] = $input_data['card']??$this->data['card'];
        $this->data['card']['from'] = $input_data['card']['from']??'';
        $this->data['card']['to'] = $input_data['card']['to']??'';
        $this->data['card']['content'] = $input_data['card']['content']??'';
        $redemption_ids = $input_data['redemption_ids']??[];
        $redemption_ids_arr = [];
        foreach ($redemption_ids as $redemption_id) {
            $redemption_ids_arr[] = $redemption_id;
        }
        $this->data['input_selected_redemption_ids'] = $redemption_ids_arr;
        if(!empty($input_data['share_uid'])){
            $api = app('ApiRequestInner',['module'=>'member']);
            $isMemberInfo = $api->request('user/exists','POST',[
                'uid'=>$input_data['share_uid'],
            ]);
            if(array_get($isMemberInfo,'code')==1){
                $this->data['share_uid'] = $input_data['share_uid'];
            }
        }
        return $this;
    }

    private function composeOneProductItem($cart_item,$product_info,$collections=[]){
        $qty = $cart_item['qty']??1;
        $total_price = bcmul($product_info['ori_price'],$qty);
        $data = [
            'cart_item_id'=>$cart_item['cart_item_id']??'',
            'product_type'=>$product_info['product_type']??0,
            'products'=>$cart_item['products']??[],
            'qty'=>$qty,
            'sku'=>$product_info['sku'],
            'name'=>$product_info['product_name'],
//            'pic'=>$product_info['kv_images'][0]['url']??'',
            'pic'=>$product_info['detail']['list_img']??'',
            'short_desc'=>$product_info['product_name'],
            'collections'=>$collections,
            'cats'=>$product_info['cats'],//所属类目
            'original_price'=>$product_info['ori_price'],
            'display_type'=>$product_info['display_type'],
            'spec_desc'=>$product_info['spec_desc']??'',
            'spec_property'=>$product_info['spec_property']??'',
            'product_id'=>$product_info['product_id']??'',
            'unique_id'=>$product_info['product_id']??'',
            'revenue_type'=>$product_info['revenue_type'],
            //默认值
            'discount'=>0,
            'applied_rule_ids'=>[],
            'paid_amount'=>$product_info['ori_price'],
            'used_points'=>0,
            'total_price'=>$total_price,
            'price_after_product_discount'=>$total_price,
            'unit_price_after_discount'=>'',
            'include_skus'=>$product_info['include_skus'],
        ];
        //如果有促销，加上促销信息
        if(isset($this->data['promotion_data'])){
            $cart_items = $this->data['promotion_data']['cartItems'];
            $cart_item_id = $cart_item['cart_item_id']??'';
            $data['applied_rule_ids'] = [];
            $data['discount'] = $cart_items[$cart_item_id]['discount']??0;
            $data['paid_amount'] = $cart_items[$cart_item_id]['final_price']??0;
            $data['used_points'] = $cart_items[$cart_item_id]['used_points']??0;
            $data['total_price'] = $cart_items[$cart_item_id]['price']??0;
            $data['price_after_product_discount'] = $cart_items[$cart_item_id]['price_after_product_discount']??
                $data['total_price'];
            $data['unit_price_after_discount'] = $cart_items[$cart_item_id]['unit_price_after_discount']??'';
            if($cart_item_id and isset($cart_items[$cart_item_id]['applied_rule_ids'])){
                $data['applied_rule_ids'] = $cart_items[$cart_item_id]['applied_rule_ids'];
            }
            //如果是套装，计算平摊
            if($collections){
                $new_collections = $this->promotion_suit_obj->avarageDiscountToSkus($data);
                $data['collections'] = $new_collections;
            }
        }
        return $data;
    }

    private function composeProductItems($main,$is_product_coupon=0,$hidden_if_sell_out=0){
        $product_data = $this->data['product_data'];
        $new_items = [];
        foreach($main as $item){
            $sku = $item['sku'];
            $product_info = $product_data[$sku];//@TODO如果商品不存在的处理
            $product_type = $product_info['product_type']??0;
            $collections = [];
            if($product_type == '2' and $is_product_coupon){//套装实物券，直接取商品的products
                $suit_data = $this->getSuitObj()->parseSuitGiftSkus($product_info['products']);
                $collections = $suit_data['suit_skus'];
                $suit_price = $suit_data['suit_price'];
                $product_info['ori_price'] = $suit_price;
            }else if($product_type == '2' ){//套装，计算价格和选中的skus
                $selected_skus = $item['products'];
                $suit_data = $this->getSuitObj()->parseSuitGiftSkus($product_info['products'],$selected_skus);
                $collections = $suit_data['suit_skus'];
                $suit_price = $suit_data['suit_price'];
                $product_info['ori_price'] = $suit_price;
            }else if($product_type == '3'){//套装礼盒
                $suit_data = $this->getSuitObj()->parseSuitGiftSkus($product_info['products']);
                $collections = $suit_data['suit_skus'];
            }
            //额外增加 $hidden_if_sell_out=1 库存不足则隐藏
            if($hidden_if_sell_out==1){
                if($product_type==1){
                    $stock = array_get($product_info,'detail.sku.stock')?:0;
                }else{
                    $stock = array_get($product_info,'detail.min_stock')?:0;
                }
                $qty = $item['qty'];
                if($stock<$qty)continue;
            }
            $new_items[] = $this->composeOneProductItem($item,$product_info,$collections);
        }
        return $new_items;
    }

    //获取套装类单例，因为data_obj只有一个
    private function getSuitObj(){
        if($this->suit_obj){
            return $this->suit_obj;
        }
        $this->suit_obj = (new Suit());
        return $this->suit_obj;
    }

    //获取商品信息，包括价格，名字等
    //组装商品数据


    public function setProductsInfo(){
        $goods = $this->data['goods_list']['goods'];
        $free_try = $this->data['goods_list']['free_try'];
        $product_coupon_sku = $this->data['product_coupon_sku']??[];
        $whole_gift_skus = $this->data['whole_gifts_skus']??[];
        //同一个促销中的赠品数量合并
        $whole_gift_skus = \App\Services\Dlc\CheckoutGiftMerge::make($whole_gift_skus);
        $another_whole_gift_skus = $this->data['another_whole_gifts_skus']??[];

        $new_goods = [];
        foreach($goods as $item){
            $main = $item['main'];
            $gifts = $item['gifts'];
            //同一个促销中的赠品数量合并
            $gifts = \App\Services\Dlc\CheckoutGiftMerge::make($gifts);
            $_gifts = $this->composeProductItems($gifts,0,1);
            $new_goods[] = [
                'main'=>$this->composeProductItems($main),
                'gifts'=>$_gifts,
                'gift_rule_name'=>$item['gifts_rule_name']??'',
            ];
        }
        foreach($another_whole_gift_skus as $item){
            //同一个促销中的赠品数量合并
            $item['gift_skus'] = \App\Services\Dlc\CheckoutGiftMerge::make($item['gift_skus']);
            $group_gifts = $this->composeProductItems($item['gift_skus'],0,1);
            //无赠品则不显示
            if(is_array($group_gifts) && count($group_gifts)){
                $this->data['goods_list']['another_group_gifts'][] = [
                    'name'=>$item['rule_name'],
                    'gifts'=>$group_gifts,
                ];
            }
        }
        $this->data['goods_list']['goods'] = $new_goods;
        $this->data['goods_list']['free_try'] = $this->composeProductItems($free_try);
        $this->data['goods_list']['group_gifts'] = $this->composeProductItems($whole_gift_skus);
        if($product_coupon_sku){
            $product_coupon_item = $this->composeProductItems($product_coupon_sku,1);
            if($product_coupon_item){
                $this->data['goods_list']['another_group_gifts'][] = [
                    'name'=>$this->data['promotion_data']['product_coupon_sku'][0]['rule_name'],
                    'gifts'=>$product_coupon_item,
                ];
            }
        }
//        $this->data['goods_list']['product_coupon_sku'] = $this->composeProductItems($product_coupon_sku,1);
        return $this;
    }

    //最新的促销信息，
    //单个商品应用的促销和折扣信息，
    //总的优惠信息
    //赠品
    //组装促销信息
    public function setPromotionInfo(){
        $promotion_info = $this->data['promotion_data'];
        //group 分组赠品
        $this->data = (new GiftSingle())->init($this->data)->loopGiftRule();
        //总的优惠信息
        $this->data['total'] = [
            'total_product_price'=>(string)floatval($promotion_info['total_discount']['total_product_price']),
            'total_discount'=>(string)floatval($promotion_info['total_discount']['total_discount']),
            'total_amount'=>(string)floatval($promotion_info['total_discount']['total_amount']),
            'total_ship_fee'=>(string)floatval($promotion_info['total_discount']['total_ship_fee']),
            'total_get_points'=>(string)floatval($promotion_info['total_discount']['total_earn_points']),
            'total_wrap_fee'=>0,
            'total_used_points'=>(string)floatval($promotion_info['total_discount']['total_point_discount']),
        ];
        //赠品
        $order_gift = $promotion_info['order_gift']??[];
        $gift = [];
        foreach($order_gift as $item){
            $gift[] = [
                'rule_id'=>$item['rule_id'],
                'gift_skus'=>$item['gift_skus'],
            ];
        }
        $this->data['gift'] = $gift;
        //试用装
        $order_freetry = $promotion_info['order_freetry']??[];
        $free_try = [];
        foreach($order_freetry as $item){
            $free_try[] = [
                'rule_id'=>$item['rule_id'],
                'gift_skus'=>$item['gift_skus'],
            ];
        }
        $this->data['free_try'] = $free_try;
        //实物券sku
        $promotion_product_coupon_sku = $promotion_info['product_coupon_sku']??[];
        $product_coupon_sku = [];
        foreach($promotion_product_coupon_sku as $item){
            $sku_str = str_replace("，",",",$item['product_coupon_sku']);
            $sku_arr = explode(',',$sku_str);
            foreach($sku_arr as $_sku){
                $product_coupon_sku[] = ['sku'=>$_sku];
            }
        }
        $this->data['product_coupon_sku'] = $product_coupon_sku;
        return $this;
    }

    //用户优惠券列表，过滤不在促销返回的优惠券列表，只显示能在当前购物车使用的
    public function replaceByPromotionCoupon(){
        $member_coupon_list = $this->data['member_info']['coupon_list'];
        $promotion_return_coupon_list  = $this->data['promotion_data']['coupon_list']??[];
        $promotion_return_coupon_id= [];
        foreach ($promotion_return_coupon_list as $item){
            $promotion_return_coupon_id[] = $item['rule_id'];
        }
        $new_coupon_list = [];
        foreach ($member_coupon_list as $item){
            $coupon_id = $item['coupon_id'];
            if(in_array($coupon_id,$promotion_return_coupon_id)){
                $new_coupon_list[] = $item;
            }
        }
        $this->data['member_info']['coupon_list'] = $new_coupon_list;
        return $this;
    }

    //下单时选择的配送地址，如果没有配送地址，可能是用户已经删除了，这是系统报错
    //同时把积分也保存起来
    public function setMemberInfo($member_info=[]){
        $this->data['member_info'] = [
            'total_points'=>$member_info['total_points']??0,
            'customer_id'=>$this->data['customer_id'],
            'pos_id'=>$member_info['pos_id']??'',
            'coupon_list'=>$member_info['coupon_list']??[],
            'shipping_address'=>$member_info['shipping_address'],
            'member_code'=>$member_info['member_code']??0,
            'store_code'=>$member_info['store_code']??0,
            'guide_code'=>$member_info['guide_code']??0,
        ];
        $this->data['origin_member_info'] = $this->data['member_info'];
        $this->data['pos_id'] = $member_info['pos_id']??'';
        return $this;
    }

    //配送地址，根据id找到配送地址信息
    public function setShippingAddress(){
        $this->data['shipping_address'] = [];
        $shipping_address = $this->data['member_info']['shipping_address']??[];
        foreach ($shipping_address as $item){
            if($item['selected']){
                $this->data['shipping_address'] = $item;
                break;
            }
        }
        return $this;
    }

    //获取积分和金额的比例
    private function getPointMoneyRate($points){
        return floor($points);
    }

    //配置使用积分
    public function setUsedPoints($member_info=[]){
        $total_points = $member_info['total_points']??0;
        $this->data['used_points'] = [
            'show'=>0,
//            'points'=>$total_points,
//            'money'=>$this->getPointMoneyRate($total_points),
            'points'=>0,
            'money'=>0,
        ];
        return $this;
    }

    //设置通过商品接口返回的商品数据
    public function setProductData($product_data=[]){
        $this->data['product_data'] = $product_data;
        return $this;
    }

    //设置通过接口返回的促销数据
    public function setPromotionData($promotion_data=[],$promotion_params){
        $this->data['promotion_data'] = $promotion_data;
        $this->data['promotion_params'] = $promotion_params;
        return $this;
    }

    //设置积分兑换的商品列表
    public function setRedemptionList($redemption_list){
        $this->data['redemption_list'] = $redemption_list;
        return $this;
    }

    //设置用户选择下单的积分兑换的商品
    public function setSelectedRedemptionList(){
        $this->data['selected_redemption_list'] = [];
//        unset($this->data['redemption_list']);
        return $this;
    }

    public function setAddress($address){
        $this->data['address'] = $address;
        return $this;
    }

    /**
     * 组装打包的sku
     * @param array $product_data
     * @return $this
     */
    public function setIncludeSkusData($product_data=[]){
        if($product_data){
            foreach($this->data['goods_list']['goods'] as &$item){
                foreach($item['main'] as $mainItem){
                    foreach($mainItem['include_skus'] as $include_sku){
                        if(array_key_exists($include_sku['sku'],$product_data)){
                            $item['main'][] = $this->composeIncludeSkusItem($product_data[$include_sku['sku']],$mainItem['sku'],$mainItem['qty']);
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * 为打包的sku设置字段(价格为0 数量同主sku)
     * @param $product_info
     * @param $main_sku
     * @param $qty
     * @return array
     */
    public function composeIncludeSkusItem($product_info,$main_sku,$qty){
        //如果主SKU是非真实SKU则打包的sku使用原价 其他情况下为0
        if((strpos($main_sku,'UNREAL_') === 0)){
            $original_price = $product_info['ori_price'];
        }else{
            $original_price = 0;
        }
        $data = [
            'cart_item_id'=>$product_info['sku'],
            'product_type'=>$product_info['product_type']??0,
            'products'=>[],
            'qty'=>$qty,
            'sku'=>$product_info['sku'],
            'name'=>$product_info['product_name'],
            'pic'=>$product_info['detail']['list_img']??'',
            'short_desc'=>$product_info['product_name'],
            'collections'=>[],
            'cats'=>$product_info['cats'],//所属类目
            'original_price'=>$original_price,
            'display_type'=>$product_info['display_type'],
            'spec_desc'=>$product_info['spec_desc']??'',
            'spec_property'=>$product_info['spec_property']??'',
            'product_id'=>$product_info['product_id']??'',
            'unique_id'=>$product_info['product_id']??'',
            'revenue_type'=>$product_info['revenue_type'],
            //默认值
            'discount'=>0,
            'applied_rule_ids'=>[],
            'paid_amount'=>0,
            'used_points'=>0,
            'total_price'=>0,
            'price_after_product_discount'=>0,
            'unit_price_after_discount'=>0,
            'main_sku'=>$main_sku
        ];
        return $data;
    }

    //如果勾选的coupon_id不存在于可用的促销中 则删除
    public function convertCouponId(){
        $coupon_id = $this->data['coupon_id'];
        if($coupon_id){
            $promotion_data = $this->data['promotion_data'];
            $rules = array_merge(array_get($promotion_data,'order_discount')?:[],array_get($promotion_data,'product_coupon_sku')?:[]);
            $ruleIds = array_column($rules,'rule_id');
            if(!in_array($coupon_id,$ruleIds)){
                $this->data['coupon_id'] = '';
                $key = RedisKey::getSingleton()->getCheckoutInfoKey($this->data['customer_id']);
                $cache_checkout_data = Redis::GET($key);
                if($cache_checkout_data){
                    $cache_checkout_data_arr = json_decode($cache_checkout_data,true);
                    if(!empty($cache_checkout_data_arr['coupon_id'])){
                        $cache_checkout_data_arr['coupon_id'] = '';
                        Redis::SET($key,json_encode($cache_checkout_data_arr));
                    }
                }
            }
        }
        return $this;
    }
}
