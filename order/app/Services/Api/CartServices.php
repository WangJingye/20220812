<?php namespace App\Services\Api;

use App\Exceptions\ApiPlaintextException;
use App\Model\Redis;
use App\Services\Api\{ProductServices};
use App\Services\Dlc\HelperService;
use App\Repositories\{CartRepository};

class CartServices
{
    public $get_car_info_obj = null;
    public $uid = '';

    public function setCartInfoObj($obj){
        $this->get_car_info_obj = $obj;
        return $this;
    }

    //把用户优惠券列表转成逗号分隔的字符串
    private function getCouponIdsStr($coupon_list_arr){
        if(!$coupon_list_arr){
            return '';
        }
        $coupon_ids = [];
        foreach ($coupon_list_arr as $coupon){
            $coupon_ids[] = $coupon['coupon_id'];
        }
        return implode(',',$coupon_ids);
    }

    /**
     * 构建购物车返回前端的数据
     * @param $uid
     * @param $checkoutItems ([['sku1'=>1],['sku2'=>2]])
     * @param $from
     * @return array
     * @throws \Exception
     */
    public function makeUpCart($uid,$checkoutItems,$from){
        $this->uid = $uid;
        $user_info = $this->getUserInfo($uid);
        //获取所有商品标识(用来请求商品详情)
        $itemKeys = $this->getAllItemKeys($checkoutItems);
        $coupon_id = $coupon_code = $error_message = '';
        //获取商品数据
        if($itemKeys){
            $products = (new ProductServices)->getProductBySku($itemKeys,$from);
            $member_coupon_list_str = $this->getCouponIdsStr($user_info['coupon_list']);
            $products = array_get($products,'data');
            if($products){
                //检查当前购物车商品数据
                if(!$this->checkValidItems($checkoutItems,$products,$diffMatch)){
                    //删除不匹配的购物车数据
                    CartRepository::delCart($uid,$diffMatch);
                    //差集取出有效的商品(过滤掉非法或者价格为0的商品)
                    $checkoutItems = array_diff_key($checkoutItems,array_flip($diffMatch));
                }
                $checkoutInfo = CartRepository::getCheckoutInfo($uid)?:$this->initCheckoutInfo();
                //获取选中优惠券
                $coupon_id = array_get($checkoutInfo??[],'coupon_id');
                $coupon_code = array_get($checkoutInfo??[],'coupon_code');
                //过滤选中的主商品
                $checkoutItemsValid = $this->getVaildItems($checkoutItems,$checkoutInfo);
                //组装促销数据($checkoutItemsValid 会过滤掉库存为0的商品和数量大于库存的商品)
                $promotionItems = $this->makeUpPromotionItems($checkoutItemsValid,$products);
                //拼装促销数据
                $promotion_data = [
                    'coupon_id'=>$coupon_id?:0,
                    'member_coupon_list'=>$member_coupon_list_str,
                    'code'=>$coupon_code?:'',
                    'total_points'=>$user_info['total_points'],
                    'used_points'=>0,
                    'page'=>'cart',
                    'cartItems'=>$promotionItems,
                    'from'=>$from,
                ];
                /** @var \App\Services\ApiRequest\Inner $api */
                $api = app('ApiRequestInner',['module'=>'promotion']);
                $promotion = $api->request('promotion/cart/applyNew','POST',$promotion_data);
                //如果优惠码无效则显示错误
                if(!empty($coupon_code) && empty($promotion['code_applied'])){
                    $coupon_code = $checkoutInfo['coupon_code'] = '';
                    $error_message = '优惠码不可用';
                }
                //重组用户信息,这里会检测当前选中的couponid并更新checkoutInfo
                $this->makeUpUserInfo($user_info,$promotion??[],$checkoutInfo);
                //将未勾选的数据合并到促销数据中
                $this->mergePromotion($promotion,$checkoutItems);
                //组装促销数据&赠品&试用装&实物券
                $goods = $this->makeUpAllItems($promotion,$products,$free_try,$product_coupon_sku,$group_gifts,$from);
                //重新排序(按照原始的排序)
                $goods = $this->sortItems($goods,$itemKeys);
                $goods_list = compact('goods','free_try','product_coupon_sku','group_gifts');
                //合并选中和填充数据
                $goods_list = $this->fillItems($goods_list,$checkoutInfo,$promotion);
                //更新选中项数据(checkoutInfo) 过滤掉非法数据
                if($uid){
                    $this->filterCheckoutInfo($uid,$checkoutItems,$checkoutInfo);
                }
            }
        }
        $goods_list = $goods_list??[];
        //总价
        $total = [
            'total_product_price'=>(string)floatval(array_get($promotion??[],'total_discount.total_product_price')),
            'total_discount'=>(string)floatval(array_get($promotion??[],'total_discount.total_discount')),
            'total_used_points'=>array_get($promotion??[],'total_discount.total_point_discount'),
            'total_wrap_fee'=>'0.00',
            'total_amount'=>(string)floatval(array_get($promotion??[],'total_discount.total_amount')),
            'total_ship_fee'=>(string)floatval(array_get($promotion??[],'total_discount.total_ship_fee')),
            'total_get_points'=>(string)floatval(array_get($promotion??[],'total_discount.total_earn_points',0)),
        ];
        $result = compact('coupon_id','coupon_code','goods_list','total','user_info','error_message');
        //试用装限制选择的数量
        $result['order_freetry_limited_qty'] = array_get($promotion??[],'order_freetry_limited_qty')?:0;
        $result['promotionInfo'] = $this->getPromotionInfoFromAd();
        $result['promotionData'] = $promotion??[];
        return $result;
    }

    /**
     * ids: SKU1,QTY|SKU2,QTY 转化成数组
     * @param $ids
     * @return mixed
     */
    public function idsToCartItems($ids){
        //拆解前端传过来的ids
        return array_reduce(explode('|',$ids),function($result,$item){
            $product = explode(',',$item);
            $result[$product[0]] = intval($product[1]);
            return $result;
        });
    }

    /**
     * 合并购物车
     * @param $uid
     * @param $items
     * @return mixed
     */
    public function mergeCart($uid,$items){
        $old_items = CartRepository::getCart($uid);
        $maxQty = CartRepository::$maxQty;
        foreach($old_items as $sku=>$qty){
            if(array_key_exists($sku,$items)){
                $sum = $items[$sku]+$qty;
                $sum = $sum>$maxQty?$maxQty:$sum;
                $items[$sku] = intval($sum);
            }else{
                $items[$sku] = intval($qty);
            }
        }
        return $items;
    }

    /**
     * 在原购物车中去除已购买的商品集合并且返回剩余商品集合
     * @param $uid
     * @param $items
     * @return mixed
     */
    public function diffCart($uid,$items){
        $old_items = CartRepository::getCart($uid);
        foreach($old_items as $sku=>$qty){
            if(array_key_exists($sku,$items)){
                $diff = $qty-$items[$sku];
                $old_items[$sku] = intval($diff);
            }
        }
        return $old_items;
    }

    /**
     * 下单完成后更新购物车(删除勾选记录)，同时需要把购物车的信息删除
     * @param $uid
     * @return bool
     */
    public function clearSelect($uid){
        $checkout_info = CartRepository::getCheckoutInfo($uid);
        CartRepository::delCheckoutInfo($uid);
        $main = $checkout_info['main']??[];
        foreach($main as $item){
            $sku = $item['sku'];
            CartRepository::delCart($uid,$sku);
        }
        return true;
    }

    /**
     * 获取购物车商品标识
     * @param $cartItems
     * @return mixed
     */
    public function getAllItemKeys($cartItems){
        $sku_keys = [];
        foreach($cartItems as $sku=>$qty){
            $suite = $this->decodeSuiteKey($sku);
            $sku_keys[] = array_get($suite,'suiteId')?:$sku;
        }
        return array_flip(array_flip($sku_keys));
    }

    /**
     * 检查购物车的对应商品是否存在 & 去除价格为0的商品
     * @param $cartItems
     * @param $products
     * @param $diffMatch (储存不能选中的商品ID)
     * @return bool
     */
    public function checkValidItems($cartItems,$products,&$diffMatch){
        $diffMatch = [];
        foreach($cartItems as $id=>$qty){
            $suite = $this->decodeSuiteKey($id);
            if($suite){
                //虚拟套装
                $flag = call_user_func(function() use($suite,$products){
                    $suite_id = $suite['suiteId'];
                    $suite_item = array_get($products,$suite_id);
                    //没有套装返回错误
                    if(empty($suite_item))return false;
                    //检查购物车的套装中的sku数量是否和套装中的所包含的数量匹配
                    $suite_skus = $suite['skus'];
                    if(count($suite_skus) != count($suite_item['products']))return false;
                    $j = 0;
                    $price = 0;
                    foreach($suite_skus as $sku){
                        //检查该单品是否存在于套装中
                        $spu_item = array_first(array_slice($suite_item['products'],$j,$j+1));
                        //找不到对应spu则返回错误
                        if(empty($spu_item))return false;
                        //获取单品详情数据
                        $sku_item = array_get(array_combine(array_column($spu_item['skus'],'sku_id'),$spu_item['skus']),$sku);
                        //找不到对应sku则返回错误
                        if(empty($sku_item))return false;
                        $price+=$sku_item['ori_price'];
                        $j++;
                    }
                    //价格为0则标识为非法商品
                    if($price<=0){
                        return false;
                    }return true;
                });
            }else{
                $product_info = array_get($products,$id);
                //判断商品接口是否返回该商品
                $flag = $product_info?true:false;
                if($flag){
                    $product_type = intval($product_info['product_type']);
                    if($product_type == 1){//普通商品
                        //价格为0则标识为非法商品
                        if($product_info['sku']['price']<=0){
                            $flag = false;
                        }
                    }elseif($product_type == 2){//套装
                        //此处的类型必然是非套装,如果是套装则标记为非法商品,可能情况比如接收的商品ID为28-2
                        $flag = false;
                    }
                }
            }
            if(!$flag){
                $diffMatch[] = $id;
            }
        }
        return count($diffMatch)?false:true;
    }

    protected function getVaildItems($checkoutItems,$checkoutInfo){
        $select_items = $this->getSelectedItems($checkoutInfo);
        $select_items = array_flip($select_items);
        return array_intersect_key($checkoutItems,$select_items);
    }

    /**
     * 拼装促销用的数据
     * @param $checkoutItems (选中的商品)
     * @param $products
     * @return array
     */
    public function makeUpPromotionItems(&$checkoutItems,$products){
        $items = [];
        foreach($checkoutItems as $id=>$qty){
            $suite = $this->decodeSuiteKey($id);
            if($suite){
                //虚拟套装
                $suite_id = $suite['suiteId'];
                $suite_item = array_get($products,$suite_id);
                //都有库存的情况下才会赋值
                $stock = $suite_item['min_stock'];
                if($stock && ($qty<=$stock)){
                    $suite_skus = $suite['skus'];
                    $j = 0;
                    $suite_price = 0;
                    foreach($suite_skus as $sku){
                        $spu_item = array_first(array_slice($suite_item['products'],$j,$j+1));
                        //获取单品详情数据
                        if($spu_item){
                            $sku_item = array_get(array_combine(array_column($spu_item['skus'],'sku_id'),$spu_item['skus']),$sku);
                            //为赠品的话则价格为0
                            if($spu_item['is_freebie']){
                                $sku_item['ori_price']= 0;
                            }
                            //计算套装总价
                            $suite_price += $sku_item['ori_price'];
                        }
                        $j++;
                    }
                    $items[] = [
                        'sku'=>$id,
                        'qty'=>$qty,
                        'mid'=>array_get($suite_item,'cats')?implode(',',$suite_item['cats']):'',
                        'price'=>(string)floatval($suite_price*$qty),
                        'unit_price'=>(string)floatval($suite_price),
                        'styleNumber'=>$suite_item['unique_id'],
                    ];
                }else{
                    //去除没库存的
                    if(array_key_exists($id,$checkoutItems)){
                        unset($checkoutItems[$id]);
                    }
                }
            }else{
                $spu_item = array_get($products,$id);
                if($spu_item) {
                    if ($spu_item['product_type'] == 3) {
                        $stock = $spu_item['min_stock'];
                        if ($stock && ($qty <= $stock)) {
                            //礼盒套装 固定SKU
                            $items[] = [
                                'sku' => $id,
                                'qty' => $qty,
                                'mid' => array_get($spu_item, 'cats') ? implode(',', $spu_item['cats']) : '',
                                'price' => (string)floatval($spu_item['sku']['ori_price'] * $qty),
                                'unit_price' => (string)floatval($spu_item['sku']['ori_price']),
                                'styleNumber' => $spu_item['unique_id'],
                            ];
                        } else {
                            //去除没库存的
                            if (array_key_exists($id, $checkoutItems)) {
                                unset($checkoutItems[$id]);
                            }
                        }
                    } else {
                        //普通商品
                        $sku_item = $spu_item['sku'];
                        $stock = $sku_item['stock'];
                        if ($stock && ($qty <= $stock)) {
                            $items[] = [
                                'sku' => $id,
                                'qty' => $qty,
                                'mid' => array_get($spu_item, 'cats') ? implode(',', $spu_item['cats']) : '',
                                'price' => (string)floatval($sku_item['ori_price'] * $qty),
                                'unit_price' => (string)floatval($sku_item['ori_price']),
                                'styleNumber' => $spu_item['unique_id'],
                            ];
                        }else {
                            //去除没库存的
                            if (array_key_exists($id, $checkoutItems)) {
                                unset($checkoutItems[$id]);
                            }
                        }
                    }
                }else {
                    //去除没库存的
                    if (array_key_exists($id, $checkoutItems)) {
                        unset($checkoutItems[$id]);
                    }
                }
            }
        }
        $i = 0;
        foreach($items as $k=>$item){
            $items[$k] = array_merge($item,[
                'cart_item_id'=>$i++,
                'priceType'=>'X',
                'labourPrice'=>0,
                'pro_type'=>'auto',
                'usedPoint'=>0,
                'discount'=>0,
                'maxUsedPoint'=>0,
            ]);
        }
        return $items;
    }

    protected function mergePromotion(&$promotion,$checkoutItems){
        $promotion_items = array_get($promotion,'cartItems')?:[];
        if($promotion_items){
            $promotion_items = array_combine(array_column($promotion_items,'sku'),$promotion_items);
        }
        foreach($checkoutItems as $id=>$qty){
            if(!array_key_exists($id,$promotion_items)){
                $promotion_items[$id] = [
                    'sku'=>$id,
                    'qty'=>$qty,
                ];
            }
        }
        $promotion['cartItems'] = $promotion_items;
    }

    /**
     * 组装最终格式数据
     * @param $promotion
     * @param $products
     * @param $free_try (返回的试用装)
     * @param $product_coupon_sku (返回的实物券)
     * @param $group_gifts
     * @param $from
     * @return array
     * @throws \Exception
     */
    public function makeUpAllItems($promotion,$products,&$free_try,&$product_coupon_sku,&$group_gifts,$from){
        $promotion_items = array_get($promotion,'cartItems');
        $promotion_items = array_combine(array_column($promotion_items,'sku'),$promotion_items);
        $promotion_gift = array_get($promotion,'order_gift');
        $promotion_freetry = array_get($promotion,'order_freetry');
        $promotion_product_coupon_sku = array_get($promotion,'product_coupon_sku');
        $free_try = $product_coupon_sku = $group_gifts = [];
        //放置所有赠品的sku(赠品&试用装&实物券)
        $giftsSkus = [
            'gift'=>[],
            'freetry'=>[],
            'product_coupon_sku'=>[],
        ];
        //获取赠品的sku
        if($promotion_gift){
            $giftsSkus['gift'] = array_reduce($promotion_gift,function($result,$item){
                $gift_skus_arr = explode(',',$item['gift_skus']);
                foreach($gift_skus_arr as $_item){
                    $result[] = $_item;
                }
                return $result;
            },[]);
        }
        //获取试用装的sku
        if($promotion_freetry){
            foreach ($promotion_freetry as $item){
                $gift_skus_arr = explode(',',$item['gift_skus']);
                foreach($gift_skus_arr as $_item){
                    $giftsSkus['freetry'][] = $_item;
                }
            }
        }
        //获取实物券
        if($promotion_product_coupon_sku){
            foreach ($promotion_product_coupon_sku as $item){
                $gift_skus_arr = explode(',',$item['product_coupon_sku']);
                foreach($gift_skus_arr as $_item){
                    $giftsSkus['product_coupon_sku'][] = $_item;
                }
            }
        }
        //合并所有需要请求商品的接口赠品sku
        $giftsSkusAll = array_unique(array_merge(
            $giftsSkus['gift'],$giftsSkus['freetry'],$giftsSkus['product_coupon_sku']
        ));
        if($giftsSkusAll){
            //获取以上赠品&试用装&实物券的商品详情
            $giftProducts = (new ProductServices)->getProductBySku($giftsSkusAll,$from);
            $giftProducts = array_get($giftProducts,'data');
        }
        //获取赠品和主商品关系数组(普通赠品跟对应的主商品在同一个层级)
        $item_gifts = [];
        $all_rules_ids = $promotion_gift?array_column($promotion_gift,'rule_id'):[];
        //全场赠品规则ID
        $group_rules = [];
        foreach($promotion_items as $id=>$promotion_item){
            $rules = array_get($promotion_item,'applied_rule_ids');
            $item_gifts[$id] = [];
            if($rules){
                foreach($rules as $rule){
                    $key = array_search($rule['rule_id'],$all_rules_ids);
                    if(($key!==false) && $this->checkRuleType($rule)){
                        if($rule['is_special_gift']!==0){
                            $item_gifts[$id] = array_merge($item_gifts[$id],explode(',',$rule['gift_skus']));
                            unset($all_rules_ids[$key]);
                        }else{
                            $group_rules[$rule['rule_id']] = $rule;
                        }
                    }
                }
            }
        }
        //组装最终框架结构
        $data_framework = $this->buildDataFramework($item_gifts);
        //填充商品详情数据
        $goods = $this->makeUpGoods($data_framework,$promotion_items,$products,$giftProducts??[]);
        //组装试用装
        if(isset($giftProducts) && !empty($giftsSkus['freetry'])){
            $free_try = $this->getData($giftsSkus['freetry'],$giftProducts,[],1);
        }
        //组装实物券
        if(isset($giftProducts) && !empty($giftsSkus['product_coupon_sku'])){
            $group_gifts[] = [
                'name'=>$promotion_product_coupon_sku[0]['rule_name'],
                'gifts'=>$this->getData($giftsSkus['product_coupon_sku'],$giftProducts)
            ];
        }
        if($group_rules){//全场促销显示
            foreach($group_rules as $group_rule){
                if($this->checkRuleType($group_rule)){//只显示type=gift
                    $group_gifts_sku = explode(',',$group_rule['gift_skus']);
                    $_gifts = $this->getData($group_gifts_sku,$giftProducts??[],[],1,1);
                    if(is_array($_gifts) && count($_gifts)){
                        $group_gifts[] = [
                            'name'=>$group_rule['rule_name'],
                            'gifts'=>$_gifts
                        ];
                    }
                }
            }
        }
        return $goods;
    }

    protected function buildDataFramework($datas){
        $data_framework = [];
        foreach($datas as $id=>$gifts){
            $data_framework[$id]['main'][] = $id;
            $data_framework[$id]['gifts'] = array_merge($data_framework[$id]['gifts']??[],$gifts) ;
        }
        return array_values($data_framework);
    }

    /**
     * 遍历填充商品详情
     * @param $data_framework
     * @param $promotion_items
     * @param $mainProducts
     * @param $giftProducts
     * @return array
     */
    protected function makeUpGoods($data_framework,$promotion_items,$mainProducts,$giftProducts){
        $goods = [];
        $i=0;
//        $same_rules = [];
//        //所有非全场的促销的促销规则只会应用在第一个商品上
//        foreach($promotion_items as &$promotion_item){
//            if(!empty($promotion_item['applied_rule_ids'])){
//                foreach($promotion_item['applied_rule_ids'] as $key=>$rule){
//                    if(!in_array($rule['rule_id'],$same_rules)){
//                        if($rule['is_special_gift']!==0){
//                            $same_rules[] = $rule['rule_id'];
//                        }
//                    }else{
//                        unset($promotion_item['applied_rule_ids'][$key]);
//                    }
//                }
//            }
//        }
        foreach($data_framework as $group_item){
            foreach($group_item as $key=>$ids){
                if($key=='main'){
                    $products = $mainProducts;
                    $goods[$i][$key] = $this->getData($ids,$products,$promotion_items);
                }elseif($key=='gifts'){
                    $products = $giftProducts;
                    $goods[$i][$key] = $this->getData($ids,$products,$promotion_items,1,1);
                }
            }
            $i++;
        }
        return $goods;
    }

    /**
     * 填充详情
     * @param $ids
     * @param $products(商品主数据)
     * @param $promotion_items(促销数据)
     * @param $type(0 默认，1 库存不足不显示)
     * @param $auto_num(根据IDS自动计算数量)
     * @return array
     */
    protected function getData($ids,$products,$promotion_items=[],$type=0,$auto_num=0){
        //合并数量
        if($auto_num==1){
            $ids_count_values = array_count_values($ids);
            foreach($ids_count_values as $k=>$v){
                $promotion_items[$k]['qty'] = $v;
            }
            $ids = array_keys($ids_count_values);
        }
        foreach($ids as $id){
            $suite = $this->decodeSuiteKey($id);
            $qty = array_get($promotion_items,"{$id}.qty")?:1;
            $price = array_get($promotion_items,"{$id}.final_price");
            $unit_price_after_discount = array_get($promotion_items,"{$id}.unit_price_after_discount")?:'';
            //显示促销信息(不显示type为freetry的)
            $applied_rule = array_reduce(array_get($promotion_items,"{$id}.applied_rule_ids")?:[],function($result,$item){
                if($this->checkRuleType($item)){
                    //全场不显示
                    $is_special_gift = array_get($item,'is_special_gift');
                    if($is_special_gift!==0){
                        $result[] = [
                            'name'=>$item['rule_name'],
                            'type'=>$item['type'],
                        ];
                    }
                }
                return $result;
            },[]);
            $collections = [];//初始化
            if($suite){
                //虚拟套装
                $suite_id = $suite['suiteId'];
                $suite_skus = $suite['skus'];
                $suite_item = array_get($products,$suite_id);
                $suite_price = $j = 0;
                foreach($suite_skus as $sku){
                    //TODO:这里有个前提假设，就是套装的顺序跟加入购物车时的大括号的顺序必须一致
                    $spu_item = array_first(array_slice($suite_item['products'],$j,$j+1));
                    //获取单品详情数据
                    if($spu_item){
                        $sku_item = array_get(array_combine(array_column($spu_item['skus'],'sku_id'),$spu_item['skus']),$sku);
                        //为赠品的话则价格为0
                        if($spu_item['is_freebie']){
                            $sku_item['ori_price']= 0;
                        }
                        //计算套装总价
                        $suite_price += $sku_item['ori_price'];
                        $collections[] = [
                            'name'=>$spu_item['product_name'],
                            'status'=>$spu_item['status'],
                            'pic'=>array_get(array_combine(array_column(array_reverse($sku_item['kv_images']),'tag'),array_reverse($sku_item['kv_images'])),'image.url'),
                            'short_desc'=>$sku_item['spec_desc'],
                            'sku'=>$sku,
                            'original_price'=>(string)floatval($sku_item['ori_price']*$qty),
                            'unit_original_price'=>(string)floatval($sku_item['ori_price']),
                            'qty'=>$qty,
                            'type'=>1,
                            'display_type'=>array_get($spu_item,'display_type'),
                            'spec_desc'=>$sku_item['spec_desc'],
                            'spec_property'=>$sku_item['spec_property'],
                            'origin_spu'=>$spu_item,
                        ];
                    }
                    $j++;
                }
                $stock = array_get($suite_item,'min_stock',0);
                $stock = $stock<0?0:$stock;
                $enough_stock = $stock>=$qty?1:0;
                if($type==1&&(!$stock||!$enough_stock))continue;
                $items[] = [
                    'name'=>$suite_item['product_name'],
                    'status'=>$suite_item['status'],
                    'pic'=>array_get(array_combine(array_column(array_reverse($suite_item['kv_images']),'tag'),array_reverse($suite_item['kv_images'])),'image.url'),
                    'short_desc'=>$suite_item['product_name'],
                    'sku'=>$suite_item['unique_id'],
                    'id'=>$suite_item['unique_id'],
                    'sku_key'=>$id,
                    'original_price'=>(string)floatval($suite_price*$qty),
                    'unit_original_price'=>(string)floatval($suite_price),
                    'price'=>(string)floatval($price?:($suite_price*$qty)),
                    'qty'=>$qty,
                    'type'=>$suite_item['product_type'],
                    'display_type'=>$suite_item['display_type'],
                    'stock'=>$stock,
                    'collections'=>$collections??[],
                    'origin_product_data'=>$suite_item,
                    'unit_price_after_discount'=>$unit_price_after_discount,
                    'promotion_msg'=>$applied_rule,
                ];
            }else{
                if(!array_key_exists($id,$products)){
                    continue;
                }
                $spu_item = $products[$id];
                if($spu_item['product_type']==3){
                    //礼盒套装 固定SKU
                    foreach($spu_item['products'] as $product){
                        foreach($product['skus'] as $sku_item){
                            $collections[] = [
                                'name'=>$product['product_name'],
                                'status'=>$product['status'],
                                'pic'=>array_get(array_combine(array_column(array_reverse($sku_item['kv_images']),'tag'),array_reverse($sku_item['kv_images'])),'image.url'),
//                                'pic'=>$product['kv_image'],//直接取spu
                                'short_desc'=>$sku_item['spec_desc'],
                                'sku'=>$sku_item['sku_id'],
                                'type'=>1,
                                'display_type'=>array_get($product,'display_type'),
                                'spec_desc'=>$sku_item['spec_desc'],
                                'spec_property'=>$sku_item['spec_property'],
                                'origin_spu'=>$product,
                            ];
                        }
                    }
                    $sku_item = $spu_item['sku'];
                    $stock = array_get($spu_item,'min_stock',0);
                    $stock = $stock<0?0:$stock;
                    $enough_stock = $stock>=$qty?1:0;
                    if($type==1&&(!$stock||!$enough_stock))continue;
                    $items[] = [
                        'name'=>$spu_item['product_name'],
                        'status'=>$spu_item['status'],
                        'pic'=>array_get(array_combine(array_column(array_reverse($spu_item['kv_images']),'tag'),array_reverse($spu_item['kv_images'])),'image.url'),
                        'short_desc'=>$spu_item['product_name'],
                        'sku'=>$id,
                        'id'=>$spu_item['unique_id'],
                        'sku_key'=>$id,
                        'original_price'=>(string)floatval($sku_item['ori_price']*$qty),
                        'unit_original_price'=>(string)floatval($sku_item['ori_price']),
                        'price'=>(string)floatval($price?:($sku_item['ori_price']*$qty)),
                        'qty'=>$qty,
                        'type'=>$spu_item['product_type'],
                        'display_type'=>$spu_item['display_type'],
                        'stock'=>$stock,
                        'collections'=>$collections??[],
                        'origin_product_data'=>$spu_item,
                        'unit_price_after_discount'=>$unit_price_after_discount,
                        'promotion_msg'=>$applied_rule,
                    ];
                }elseif($spu_item['product_type']==1){
                    //普通商品
                    $sku_item = $spu_item['sku'];
                    $stock = array_get($sku_item,'stock',0);
                    $stock = $stock<0?0:$stock;
                    $enough_stock = $stock>=$qty?1:0;
                    if($type==1&&(!$stock||!$enough_stock))continue;
                    $items[] = [
                        'name'=>$spu_item['product_name'],
                        'status'=>$spu_item['status'],
                        'pic'=>array_get($spu_item,'list_img'),
                        'short_desc'=>array_get($spu_item,'product_name',''),
                        'sku'=>$id,
                        'id'=>$spu_item['unique_id'],
                        'sku_key'=>$id,
                        'original_price'=>(string)floatval($sku_item['ori_price']*$qty),
                        'unit_original_price'=>(string)floatval($sku_item['ori_price']),
                        'price'=>(string)floatval($price?:($sku_item['ori_price']*$qty)),
                        'qty'=>$qty,
                        'type'=>$spu_item['product_type'],
                        'display_type'=>$spu_item['display_type'],
                        'spec_desc'=>$sku_item['spec_desc'],
                        'spec_property'=>$sku_item['spec_property'],
                        'stock'=>$stock,
                        'origin_product_data'=>$spu_item,
                        'unit_price_after_discount'=>$unit_price_after_discount,
                        'promotion_msg'=>$applied_rule,
                    ];
                }else{
                    //来自于实物券的套装 因为只传了套装ID所以默认都用第一个规格
                    if($spu_item['product_type']==2){
                        //虚拟套装
                        $suite_item = $spu_item;
                        $products = $spu_item['products'];
                        $suite_price = 0;
                        foreach($products as $spu_item){
                            $sku_item = array_first($spu_item['skus']);
                            //为赠品的话则价格为0
                            if($spu_item['is_freebie']){
                                $sku_item['ori_price']= 0;
                            }
                            //计算套装总价
                            $suite_price += $sku_item['ori_price'];
                            $collections[] = [
                                'name'=>$spu_item['product_name'],
                                'status'=>$spu_item['status'],
                                'pic'=>array_get(array_combine(array_column(array_reverse($sku_item['kv_images']),'tag'),array_reverse($sku_item['kv_images'])),'image.url'),
//                            'pic'=>$spu_item['kv_image'],//这里直接取spu的image
                                'short_desc'=>$sku_item['spec_desc'],
                                'sku'=>$sku_item['sku_id'],
                                'original_price'=>(string)floatval($sku_item['ori_price']*$qty),
                                'unit_original_price'=>(string)floatval($sku_item['ori_price']),
                                'qty'=>$qty,
                                'type'=>1,
                                'display_type'=>array_get($spu_item,'display_type'),
                                'spec_desc'=>$sku_item['spec_desc'],
                                'spec_property'=>$sku_item['spec_property'],
                                'origin_spu'=>$spu_item,
                            ];
                        }
                        $stock = array_get($suite_item,'min_stock',0);
                        $stock = $stock<0?0:$stock;
                        $enough_stock = $stock>=$qty?1:0;
                        if($type==1&&(!$stock||!$enough_stock))continue;
                        $items[] = [
                            'name'=>$suite_item['product_name'],
                            'status'=>$suite_item['status'],
                            'pic'=>array_get(array_combine(array_column(array_reverse($suite_item['kv_images']),'tag'),array_reverse($suite_item['kv_images'])),'image.url'),
                            'short_desc'=>$suite_item['product_name'],
                            'sku'=>$suite_item['unique_id'],
                            'id'=>$suite_item['unique_id'],
                            'sku_key'=>$id,
                            'original_price'=>(string)floatval($suite_price*$qty),
                            'unit_original_price'=>(string)floatval($suite_price),
                            'price'=>(string)floatval($price?:($suite_price*$qty)),
                            'qty'=>$qty,
                            'type'=>$suite_item['product_type'],
                            'display_type'=>$suite_item['display_type'],
                            'stock'=>$stock,
                            'collections'=>$collections??[],
                            'origin_product_data'=>$suite_item,
                            'unit_price_after_discount'=>$unit_price_after_discount,
                            'promotion_msg'=>$applied_rule,
                        ];
                    }
                }
            }
        }
        return $items??[];
    }

    //用户输入的选中的items
    private function getInputSelectedItems(){
        if(!$this->get_car_info_obj){
            return [];
        }
        return $this->get_car_info_obj->getSelectedIds();
    }

    //用户输入选中的free items
    private function getInputSelectedFreeItems(){
        if(!$this->get_car_info_obj){
            return [];
        }
        return $this->get_car_info_obj->getSelectedFreeIds();
    }

    //包括登录用户和未登录时传入购物车的ids
    private function getSelectedItems($checkout_info){
        $checkoutInfo_items = array_get($checkout_info,'main');
        $select_items = $checkoutInfo_items?array_column($checkoutInfo_items,'sku'):[];
        $select_items = array_merge($select_items,$this->getInputSelectedItems());
        return $select_items;
    }

    //包括登录用户和未登录时传入购物车的free_ids
    private function getSelectedFreeItems($checkout_info){
        $checkoutInfo_free_items = array_get($checkout_info,'free_try')?:[];
        $select_items = $checkoutInfo_free_items?array_column($checkoutInfo_free_items,'sku'):[];
        return array_merge($select_items,$this->getInputSelectedFreeItems());
    }

    /**
     * 合并选中并填充
     * @param $goods_list
     * @param $checkoutInfo
     * @param $promotion
     * @return array
     */
    public function fillItems($goods_list,&$checkoutInfo,$promotion){
        //普通商品选中合并
        $select_items = $this->getSelectedItems($checkoutInfo);
        $goods = $goods_list['goods'];
        foreach($goods as $k=>$goods_item){
            foreach($goods_item['main'] as $kk=>$item){
                //上架&库存不为空&库存大于等于数量才能选中
                $stock = intval($goods[$k]['main'][$kk]['stock']);
                $qty = intval($goods[$k]['main'][$kk]['qty']);
                if($item['status']==1 && ($stock>0) && ($stock>=$qty)){
                    $goods[$k]['main'][$kk]['selected'] = (in_array($item['sku_key'],$select_items))?1:0;
                }else{
                    //不满足以上状态则去除选中
                    $goods[$k]['main'][$kk]['selected'] = 0;
                    if(in_array($item['sku_key'],$select_items)){
                        $key = array_search($item['sku_key'],$select_items);
                        unset($select_items[$key]);
                    }
                }
            }
        }
        $select_items = array_flip(array_flip($select_items));
        $checkoutInfo['main'] = array_reduce($select_items,function($result,$item){
            $result[] = ['sku'=>$item];
            return $result;
        },[]);

        //限制试用装数量
        $free_try_limit = $promotion['order_freetry_limited_qty']??0;
        if(sizeof(array_get($checkoutInfo,'free_try',[]))>$free_try_limit){
            $checkoutInfo['free_try'] = array_slice($checkoutInfo['free_try'],0,$free_try_limit);
        }
        //试用装选中合并
        $select_items = $this->getSelectedFreeItems($checkoutInfo);
        $free_try = $goods_list['free_try'];
        foreach($free_try as $k=>$item){
            $free_try[$k]['selected'] = (in_array($item['sku_key'],$select_items))?1:0;
        }
        //实物券
        $product_coupon_sku = $goods_list['product_coupon_sku'];
        //全局赠品
        $group_gifts = $goods_list['group_gifts'];
        return compact('goods','free_try','group_gifts','product_coupon_sku');
    }

    /**
     * 将套装ID和套装中的sku转换为拥有特殊标记的字符串
     * @param $suiteId
     * @param array $skus
     * @return string e.g:{{套装ID}}{{SKU1}}{{SKU2}}
     */
    public function encodeSuiteKey($suiteId,array $skus){
        $key = "{{{$suiteId}}}";
        foreach($skus as $sku){
            $key .= ("{{{$sku}}}");
        }
        return $key;
    }

    /**
     * 从字符串中解析出套装ID和套装中的sku,非套装返回false
     * @param $key (e.g:{{套装ID}}{{SKU1}}{{SKU2}})
     * @return array|bool
     */
    public function decodeSuiteKey($key){
        $regex = "/(?<={{)[^}}]+/";
        if(preg_match_all($regex, $key, $match)){
            if(count($match[0])>=1){
                $suiteId = array_shift($match[0]);
                $skus = $match[0];
                return compact('suiteId','skus');
            }
        }return false;
    }

    /**
     * 获取购物车勾选填充数据
     * @param $uid
     * @return array|mixed|object
     */
    public static function getCheckoutInfo($uid){
        $checkoutInfo = CartRepository::getCheckoutInfo($uid);
        if($checkoutInfo){
            $checkoutInfoItems = array_get($checkoutInfo,'main');
            if($checkoutInfoItems){
                $cartItems = CartRepository::getCart($uid);
                foreach($checkoutInfoItems as $k=>$item){
                    $checkoutInfoItems[$k]['qty'] = array_get($cartItems,$item['sku'],0);
                }
                $checkoutInfo['main'] = $checkoutInfoItems;
            }
        }
        return $checkoutInfo;
    }

    /**
     * @param $uid
     * @return null
     * @throws \Exception
     */
    public function getUserInfo($uid){
        /** @var \App\Services\ApiRequest\Inner $api */
//        $api = app('ApiRequestInner',['module'=>'member']);
//        $resp = $api->request('user/getMemberInfo','POST',[
//            'user_id'=>$uid,
//            'no_crm'=>'1',
//        ]);
//        return [
//            'coupon_list'=>array_get($resp,'data.coupon_list')?:[],
//            'total_points'=>array_get($resp,'data.total_points')?:0,
//        ];

        $api = app('ApiRequestInner',['module'=>'member']);
        $resp = $api->request('coupon/getMemberInfo','POST',[
            'uid'=>$uid,
        ]);
        return [
            'coupon_list'=>array_get($resp,'data.coupon_list')?:[],
            'total_points'=>array_get($resp,'data.total_points')?:0,
        ];
    }

    //保存购物车选中商品
    public function updateSelect($data){
        $uid = $data['uid'];
        $coupon_id = $data['coupon_id'];
        $coupon_code = $data['coupon_code'];
        $skus = $data['skus'];
        $free_skus = $data['free_skus'];
        $flag = $data['flag'];//0，删除，1添加

        $checkout_info = CartRepository::getCheckoutInfo($uid);
        $main = $checkout_info['main']??[];
        $free_try = $checkout_info['free_try']??[];
        $main_skus = array_column($main,'sku');
        $free_try_skus = array_column($free_try,'sku');
        if($flag){//添加
            foreach($skus as $sku){
                if(!in_array($sku['sku'],$main_skus)){//防止重复
                    $main[] = ['sku'=>$sku['sku']];
                }
            }
            foreach ($free_skus as $sku){
                if(!in_array($sku['sku'],$free_try_skus)){//防止重复
                    $free_try[] = ['sku'=>$sku['sku']];
                }
            }
        }else{//删除
            $input_skus = [];
            foreach($skus as $item){
                $input_skus[] = $item['sku'];
            }
            foreach($main as $key=>$item){
                if(in_array($item['sku'],$input_skus)){
                    unset($main[$key]);
                }
            }
            //free try
            $input_free_try_skus = [];
            foreach($free_skus as $item){
                $input_free_try_skus[] = $item['sku'];
            }
            foreach($free_try as $key=>$item){
                if(in_array($item['sku'],$input_free_try_skus)){
                    unset($free_try[$key]);
                }
            }
        }
        $checkout_info['main'] = $main;
        $checkout_info['free_try'] = $free_try;
        //直接获取前端给的优惠券和优惠码
        $checkout_info['coupon_code'] = filtersc($coupon_code);
        $checkout_info['coupon_id'] = intval($coupon_id);
        if(!isset($checkout_info['checkout'])){//初始化checkout部分
            $checkout_info['checkout'] = [
                'channel'=>'',
                'shipping_address_id'=>'',
                'used_points'=>'',
                'shipping_method'=>'',
                'payment_method'=>'',
                'accepted_flag'=>'',
                'guide'=>'',//导购
                'invoice'=>[
                    'type'=>'',
                    'id'=>'',
                    'title'=>'',
                    'email'=>'',
                ],
                'card'=>[
                    'from'=>'',
                    'to'=>'',
                    'content'=>'',
                ],
            ];
        }
        //保存checkout info
        CartRepository::setCheckoutInfo($uid,$checkout_info);
    }

    private function initCheckoutInfo(){
        return [
            'main'=>[],
            'free_try'=>[],
            'coupon_code'=>'',
            'coupon_id'=>'',
            'channel'=>'',
            'shipping_address_id'=>'',
            'used_points'=>'',
            'shipping_method'=>'',
            'payment_method'=>'',
            'accepted_flag'=>'',
            'guide'=>'',//导购
            'invoice'=>[
                'type'=>'',
                'id'=>'',
                'title'=>'',
                'email'=>'',
            ],
            'card'=>[
                'from'=>'',
                'to'=>'',
                'content'=>'',
            ],
        ];
    }

    //未登录购物车缓存，选中的商品，登录的时候，需要放到checkout_info选中里面
    public function updateBrowserSelected($uid){
        $input_selected_skus = $this->getInputSelectedItems();
        $skus = [];
        foreach ($input_selected_skus as $item){//组装为需要的格式
            $skus[] = [
                'sku'=>$item,
            ];
        }
        $data = [
            'uid'=>$uid,
            'coupon_id'=>'',
            'coupon_code'=>'',
            'skus'=>$skus,
            'free_skus'=>[],
            'flag'=>1,
        ];
        $this->updateSelect($data);
    }

    //删除checkout info里面的sku,当删除购物车的时候，需要删除
    public function delCheckoutInfoSku($uid,$sku){
        $checkout_info = CartRepository::getCheckoutInfo($uid);
        $main = $checkout_info['main']??[];
        if(!$main){//没有main, nothing to do here
            return ;
        }
        $has_flag = 0;
        foreach ($main as $key=>$item){
            if($item['sku'] == $sku){
                unset($main[$key]);
                $has_flag = 1;
                break;
            }
        }
        if(!$has_flag){
            return ;
        }
        $checkout_info['main'] = $main;
        CartRepository::setCheckoutInfo($uid,$checkout_info);
    }

    public function addCheckoutInfoSku($uid,$sku){
        $checkout_info = CartRepository::getCheckoutInfo($uid);
        $main = $checkout_info['main']??[];
        $main = $main?array_combine(array_column($main,'sku'),$main):[];
        if(!array_key_exists($sku,$main)){
            $main[$sku] = compact('sku');
            $checkout_info['main'] = array_values($main);
            CartRepository::setCheckoutInfo($uid,$checkout_info);
        }
    }

    public function replaceCheckoutInfoSku($uid,$old_sku,$new_sku){
        $checkout_info = CartRepository::getCheckoutInfo($uid);
        $main = $checkout_info['main']??[];
        $main = $main?array_combine(array_column($main,'sku'),$main):[];
        if(array_key_exists($old_sku,$main)){
            $main[$old_sku] = ['sku'=>$new_sku];
            $checkout_info['main'] = array_values($main);
            CartRepository::setCheckoutInfo($uid,$checkout_info);
        }
    }

    protected function filterCheckoutInfo($uid,$checkoutItems,$checkoutInfo){
        $ori_checkout_info = CartRepository::getCheckoutInfo($uid);

        //检查checkoutinfo中的选中项是否存在于cart中 如果不存在则删除
        foreach($checkoutInfo['main'] as $key=>$item){
            if(!array_key_exists($item['sku'],$checkoutItems)){
                unset($checkoutInfo['main'][$key]);
            }
        }
        //如果和原先的有差别则更新
        if($ori_checkout_info!=$checkoutInfo){
            $checkoutInfo['main'] = array_values($checkoutInfo['main']);
            CartRepository::setCheckoutInfo($uid,$checkoutInfo);
        }
    }

    /**
     * 返回前端的优惠券必须包含在促销接口的优惠券中
     * @param $user_info
     * @param $promotions
     * @param $checkoutInfo
     */
    protected function makeUpUserInfo(&$user_info,$promotions,&$checkoutInfo){
        $coupon_list = array_get($user_info,'coupon_list')?:[];
        $allow_coupon_list = array_column(array_get($promotions,'coupon_list')?:[],'rule_id');
        if($coupon_list){
            foreach($coupon_list as $key=>$coupon){
                if(!in_array($coupon['coupon_id'],$allow_coupon_list)){
                    unset($coupon_list[$key]);
                }
            }
        }
        //检测当前选中的coupon_id
        $coupon_id = array_get($checkoutInfo,'coupon_id');
        if($coupon_id && !in_array($coupon_id,$allow_coupon_list)){
            $checkoutInfo['coupon_id'] = '';
        }
        $user_info['coupon_list'] = array_values($coupon_list);
    }

    /**
     * 检查库存
     * @param $uid
     * @param $checkoutItems
     * @param $from
     * @return bool
     * @throws \Exception
     */
    public function checkStock($uid,$checkoutItems,$from){
        $itemKeys = $this->getAllItemKeys($checkoutItems);
        if($itemKeys){
            $products = (new ProductServices)->getProductBySku($itemKeys,$from);
            $products = array_get($products,'data');
            if($uid){
                $checkoutInfo = CartRepository::getCheckoutInfo($uid)?:$this->initCheckoutInfo();
                //过滤选中的主商品
                $checkoutItems = $this->getVaildItems($checkoutItems,$checkoutInfo);
            }return $this->checkStockHandle($checkoutItems,$products);
        }throw new ApiPlaintextException('购物车为空');
    }

    /**
     * @param $checkoutItems
     * @param $products
     * @return bool
     */
    protected function checkStockHandle($checkoutItems,$products){
        foreach($checkoutItems as $id=>$qty){
            $suite = $this->decodeSuiteKey($id);
            if($suite){
                //虚拟套装
                $suite_id = $suite['suiteId'];
                $suite_item = array_get($products,$suite_id);
                if($qty>$suite_item['min_stock']){
                    return false;
                }
            }else{
                $spu_item = array_get($products,$id);
                if($spu_item['product_type'] == 1){
                    //普通商品
                    $sku_item = $spu_item['sku'];
                    if($qty>$sku_item['stock']){
                        return false;
                    }
                }elseif($qty>$spu_item['min_stock']){
                    return false;
                }
            }
        }return true;
    }

    protected function checkRuleType($rule){
        if(($rule['type']=='gift')||(($rule['type']=='code') && ($rule['sub_type']=='code_gift'))){
            if($rule['type']!='freetry'){
                return true;
            }
        }return false;
    }

    private function getPromotionInfo($promotion){
        $promotionInfo = [];
        if($promotion){
            $rules = array_get($promotion,'rules');
            if($rules){
                $text = array_column($rules,'name');
                $promotionInfo['title'] = '促销信息';
                $promotionInfo['text'] = implode(';',$text);
            }
        }
        return $promotionInfo;
    }

    private function getPromotionInfoFromAd(){
        $ad = HelperService::getAd('cart_promotion_info');
        $adInfo  = [];
        if($ad){
            foreach($ad as $key=>$info){
                for($i=1;$i<=10;$i++){
                    $line = array_get($info,"data{$i}");
                    if($line){$data[] = $line;}
                }
                if(isset($data)){
                    $text = implode(";",$data);
                }
                $adInfo[] = [
                    'title'=>$info['name'],
                    'text'=>$text??'',
                ];
            }
        }
        return $adInfo;
    }

    private function sortItems($goods,$itemKeys){
        $_goods = [];
        foreach($goods as $item){
            $key = $item['main'][0]['sku_key'];
            $_goods[$key] = $item;
        }
        $new_goods = [];
        foreach($itemKeys as $sku_key){
            if(array_key_exists($sku_key,$_goods)){
                $new_goods[] = $_goods[$sku_key];
            }
        }
        return $new_goods;
    }
}
