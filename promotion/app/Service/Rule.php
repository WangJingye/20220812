<?php
namespace App\Service;

use App\Service\Rule\Product\ProductDiscount;
use App\Service\Rule\Product\NpieceNdiscount;
use App\Service\Rule\Order\OrderNDiscount;
use App\Service\Rule\Order\FullReductionOfOrder;
use App\Service\Rule\Coupon\Coupon;
use App\Service\Rule\Coupon\CouponList;
use App\Service\Rule\Code\Code;
use App\Service\Rule\Gift;
use App\Service\Rule\Init;
use App\Service\Rule\Point\Earn;
use App\Service\Rule\Point\Auto;
use App\Service\Rule\Point\ManualPoints;
use App\Service\Rule\Point\LimitedPoint;
use App\Service\Rule\Total\Total;
use App\Model\Promotion\Gift as ModelGift;
use App\Model\Promotion\Cart;
use App\Service\RuleFactory;
use App\Service\Rule\FreeTry\Limited;
use App\Lib\Http;


class Rule
{
    protected $cartItems=[];
    protected $rules = [];
    protected $data = [];
    protected $gifts = [];
    protected $gift_stock = [];
    protected $can_use_points_cids = ['GF','PF','MP','DF','DI','XF','GS','QF','SS','TF','PL','FJ','SF',];
    
    
    function __construct($cart){
        $this->data= $cart;
        $this->getAllActiveRules();
        $this->getAllActiveGifts();
        $this->getAllGiftStock();
        $this->convertStyleNumberToUpper();
        bcscale(2);
    }
    //把数据库的排除款号，增加款号，item输入的款号都变为大写
    public function convertStyleNumberToUpper(){
        $rules = $this->rules;
        $new_item = [];
        foreach($rules as $rule){
            $rule['exclude_sku'] = strtoupper($rule['exclude_sku']);
            $rule['add_sku'] = strtoupper($rule['add_sku']);
            $new_item[] = $rule;
        }
        $this->rules = $new_item;
        //
        $cartItems = $this->data['cartItems'];
        $new_car_items = [];
        foreach($cartItems as $item){
            $item['styleNumber'] = strtoupper($item['styleNumber']);
            $new_car_items[] = $item;
        }
        $this->data['cartItems'] = $new_car_items;
    }
    
    //优先级排序
    public function sortPriority($items){
        $sort_key = [];
        foreach($items as $item){
            $sort_key[] = $item['priority'];
        }
        array_multisort($sort_key, SORT_DESC, $items);
        $items = $this->topShipFeeTry($items);
        return $items;
    }
    //付邮试用放最前面
    public function topShipFeeTry($items){
        $ship_fee_try = [];
        foreach($items as $key=>$item){
            if($item['type'] == 'ship_fee_try'){
                $ship_fee_try[] = $item;//优先级最大的放在最后
                unset($items[$key]);
            }
        }
        krsort($ship_fee_try);
        foreach($ship_fee_try as $item){
            array_unshift($items,$item);
        }
        return $items;
    }
    
    public function getAllActiveRules(){
        if($this->rules){
            return $this->rules;
        }
        $curr_time = date('Y-m-d H:i:s');
        $where = [
            ['status','=',2,],['start_time','<',$curr_time],['end_time','>',$curr_time],//激活，有效的
        ];
        $rules = Cart::where($where)
            ->get()->toArray();
        $rules = $this->sortPriority($rules);
        $this->rules = $rules;
        return $this->rules;
    }

    //获取赠品的库存
    public function getAllGiftStock(){
        $channel = $this->data['from']??1;
        $skus = [];
        if(count($this->gifts) == 0){
            return;
        }
        $gifts = $this->gifts;
        foreach($gifts as $item){
            if(!$item['gwp_skus']){
                continue;
            }
            $gift_sku_arr = explode(',',$item['gwp_skus']);
            foreach($gift_sku_arr as $_gift_sku){
                $skus[] = $_gift_sku;
            }
        }
        if(!$skus){
            return;
        }
        $http = new Http();
        $input_params = [
            'sku_ids'=>implode(',',$skus),
            'from'=>$channel,
        ];
        $products = $http->curl('outward/product/getProductInfoBySkuIds',$input_params);
        $product_data = $products['data']??[];
        $gift_stock = [];
        foreach($product_data as $_sku=>$item){
            $min_stock = (int)array_get($item,'sku.stock');
            $gift_stock[$_sku] = $min_stock;
        }
        $this->gift_stock = $gift_stock;
        return $this->gift_stock;
    }

    public function getAllActiveGifts(){
        if($this->gifts){
            return $this->gifts;
        }
        $rules = $this->rules;
        $gifts = [];
        foreach($rules as $rule){
            if($rule['type'] == 'gift' or $rule['type'] == 'code_gift'){
                $gifts[] = $rule;
            }
        }
        $this->gifts = $gifts;
        return $this->gifts;
    }
    
    
    function apply(){
        $this->data['total_discount'] = [
            'total_product_price' => '0.00',//商品总价
            'total_product_discount' => '0.00',//商品折扣
            'total_order_discount' =>  '0.00',//满减折扣
            'total_coupon_discount' => '0.00',//优惠券折扣
            'total_code_discount' => '0.00',//优惠码折扣
            'total_member_discount' => '0.00',//会员优惠
            'total_point_discount' => '0.00',//悦享钱优惠
            'total_discount' => '0.00',//总折扣价格
            'salesPdt'=>'0.00',//商品折后价，没有下划线的价格（包括商品折扣，会员折扣，悦享钱）
            'total_ship_fee' => '0.00',//运费
            'total_amount' => '0.00',//合计
        ];
        $this->data['points'] = [//悦享钱
            'earn'=>0,//可以赚取
            'highest_used'=>'',//最高可使用
            'best_price_used'=>'',//订单最低价格，使用
        ]; 
        $this->data['coupon_list'] = [];
        $this->data['rules'] = $this->rules;
        $this->data['gifts'] = $this->gifts;
        $this->data['gift_stock'] = $this->gift_stock;
        $this->data = (new Init())->setData($this->data)->floorPrice();
        $this->data = (new CouponList())->setData($this->data)->collect();
        //额外填充displayname
        \App\Service\Dlc\Rule::fill($this->rules);
        $this->process();
        $this->data = (new Auto)->process($this->data);
        $this->data = (new Total())->process($this->data);
        $this->data = (new Auto())->averageToItems($this->data);
        $this->data = (new Limited())->getLimitedQty($this->data);
        //运费根据传入的省改变
        $this->data = (new \App\Service\Dlc\Shipfee)->process($this->data);

        unset($this->data['gifts']);
        return $this->data;
    }

    //按照优先级排序，每个规则应用一遍所有商品，为了性能，创建单例
    public function process(){
        $rules = $this->rules;
        $rule_factory = (new RuleFactory());
        foreach($rules as $rule){
            $rule_type = $rule['type'];
            $this->data = $rule_factory->getSingleton($rule_type)->setData($this->data)->setRule($rule)->collect();
        }
    }
}











