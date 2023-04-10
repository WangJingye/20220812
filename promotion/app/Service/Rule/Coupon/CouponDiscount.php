<?php
namespace App\Service\Rule\Coupon;
use App\Service\Rule\RuleAbstract;
use App\Service\Rule\CrossRuleCheckTrait;
//直接折扣-优惠券
//矩阵，可以商品折扣，满减，优惠码，会员折扣，叠加
class CouponDiscount extends RuleAbstract
{
    use CrossRuleCheckTrait;
    protected $cartTotalPrice;

    public function collect()
    {
        $this->collectOne($this->rule);
        return $this->data;
    }
    public function collectOne($rule){
        $input_coupon_id = $this->data['coupon_id'];
        if($rule['id'] != $input_coupon_id){
            return ;
        }
        if(!$this->couponValidate($rule)){
            return;
        }
        $this->deal($rule);
    }
    
    // 每个规则应用一遍每一个商品
    protected function collectByPriority()
    {
        $rules = $this->data['rules'];
        $coupon_ids = $this->data['coupon_id'];
        $coupon_id_arr = explode(',',$coupon_ids);
        foreach ($rules as $rule) {
            if(!in_array($rule['id'],$coupon_id_arr)){
                continue;
            }
            if($rule['type'] != 'coupon'){
                continue;
            }
            if(!$this->couponValidate($rule)){
                continue;
            }
            $this->deal($rule);
        }
        //如果优惠券是自动最优查找到的，而且最后没有应用（由于叠加原因），不把coupon_id返回
        if(isset($this->data['best_coupon_id']) and $this->data['best_coupon_id']){
            if($this->data['total_discount']['total_coupon_discount'] == '0.00'){//没有应用
                $this->data['coupon_id'] = '';
            }
        }
    }
    //没有指定的情况下，找到顾客有效优惠券中，最大折扣的
    public function findBestCoupon(){
        $input_coupon_id = $this->data['coupon_id'];
        $input_coupon_list = $this->data['coupon_list'];
        if($this->data['coupon_id'] ){
            foreach($input_coupon_list as $item){
                if($item['rule_id'] == $input_coupon_id){//而且传入的coupon是可以用在当前购物车的
                    return $this->data;
                }
            }
        }
        $coupon_list = $this->data['coupon_list'];
        $member_coupon_list = $this->data['member_coupon_list'];
        if(!$member_coupon_list or !$coupon_list){
            return $this->data;
        }
        $member_coupon_list_arr = explode(',',$member_coupon_list);
        $coupon_discount = 0;
        $best_coupon = '';
        foreach($coupon_list as $coupon){
            if(in_array($coupon['rule_id'],$member_coupon_list_arr)){
                if($coupon['coupon_discount'] > $coupon_discount){
                    $coupon_discount = $coupon['coupon_discount'];
                    $best_coupon = $coupon;
                }
            }
        }
        if($best_coupon){
            $this->data['coupon_id'] = $best_coupon['rule_id'];
            $this->data['best_coupon_id'] = $best_coupon['rule_id'];
        }
        return $this->data;
    }
    //
    public function couponValidate($rule){
        $input_coupon_id = $this->data['coupon_id'];
        $member_coupon_list = $this->data['member_coupon_list'];
        $member_coupon_list_arr = explode(',',$member_coupon_list);
        if(!in_array($input_coupon_id,$member_coupon_list_arr)){
            return false;
        }
        return true;
    }
    //
    public function deal($rule){
        $cartItems = $this->data['cartItems'];
        $newItem = [];
        foreach($cartItems as $item){
            $flag = $this->condition->check($rule, $item);
            if(!$flag){
                $newItem[] = $item;
                continue;
            }
            $discount = $rule['product_discount'];
            $labour_price = floor($item['labourPrice']);
            $price = floor($item['price']);
            $discount_price = 0;
            if($item['priceType'] == 'Y'){//计价，工费
                $item['labour_price_discount'] = $item['labour_price_discount']??0;
                $labour_price = bcsub($labour_price,$item['labour_price_discount']);//折扣后工费
                $labour_price_dicount = bcmul($labour_price,$discount/100);
                $discount_price = bcsub($labour_price, $labour_price_dicount);
                $item['labour_price_discount'] = bcadd($item['labour_price_discount'],$discount_price);
            }else{
                $price = bcsub($price,$item['discount']);//折扣后价格
                $price_discount = bcmul($price,$discount/100);
                $discount_price = bcsub($price, $price_discount);
            }
            $item['discount'] = bcadd($item['discount'], $discount_price);
            $this->data['total_discount']['total_coupon_discount'] = bcadd(
                $this->data['total_discount']['total_coupon_discount'],
                $discount_price
            );
            $item['applied_rule_ids'][] = [
                'rule_id'=>$rule['id'],
                'discount'=>$discount_price,
                'rule_name'=>$rule['name'],
                'display_name'=>$rule['display_name'],
                'extend_name'=>array_get($rule,'extend_name'),
                'type'=>'coupon',
                'sub_type'=>'coupon_discount',
            ];
            $newItem[] = $item;
        }
        $this->data['cartItems'] = $newItem;
    }
    
       

}

