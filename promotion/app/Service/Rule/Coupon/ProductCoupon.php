<?php
namespace App\Service\Rule\Coupon;
use App\Service\Rule\RuleAbstract;
use App\Service\Rule\CrossRuleCheckTrait;

//实物优惠券， 随单礼券没有使用条件，没有叠加限制
class ProductCoupon extends RuleAbstract
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
        $cart_items = $this->data['cartItems'];
        if($rule['type'] != 'product_coupon'){
            return false;
        }
        foreach($cart_items as $item){
            if($this->condition->check($rule, $item) == false){
                $newItem[] = $item;
                continue;
            }else{
                $this->data['product_coupon_sku'][] = [
                    'rule_id'=>$rule['id'],
                    'rule_name'=>$rule['name'],
                    'display_name'=>$rule['display_name'],
                    'product_coupon_sku'=>$rule['product_coupon_sku'],
                ];
            }
        }
    }
    
       

}

