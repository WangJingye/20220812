<?php
namespace App\Service\Rule\Coupon;
use App\Service\Rule\RuleAbstract;
use App\Service\Rule\CrossRuleCheckTrait;
//优惠券
//矩阵，可以商品折扣，满减，优惠码，会员折扣，叠加
class Coupon extends RuleAbstract
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
        if($rule['type'] != 'coupon'){
            return false;
        }
        //是否满足满xxxx，计算总价，需要计算之前折扣后的价格
        $cart_items = $this->data['cartItems'];
        $total_amount = 0;
        $cids = explode(',',$rule['cids']);
        $excludes_style_number = explode(',',$rule['exclude_sku']);
        $includes_style_number = explode(',',$rule['add_sku']);
        foreach($cart_items as $item){
            if($this->condition->check($rule, $item)){
                $labour_price = $item['labourPrice'];
                $price = $item['price'];
                $discount = $item['discount'];
                $total_price = bcadd($labour_price,$price);
                $total_price = bcsub($total_price, $discount); 
                $total_amount = bcadd($total_amount,$total_price);
            }
        }
        if(!$total_amount){
            return false;
        }
        //计算最接近的阶梯
        $rule_total_amount = explode(',',$rule['total_amount']);
        $rule_total_discount = explode(',',$rule['total_discount']);
        if($total_amount < min($rule_total_amount)){
            return false;
        }
        $rule_total_discount = $rule_total_discount[0];//取第一个
        $this->data['order_discount'][] = ['rule_id'=>$rule['id'],'rule_name'=>$rule['name'],'display_name'=>$rule['display_name'],'discount'=>$rule_total_discount];
        $this->data['total_discount']['total_coupon_discount'] = bcadd($this->data['total_discount']['total_coupon_discount'],
                                $rule_total_discount);
        //平分每个满减到满足条件的item上
        //todo: 是否平均分到所有满足的，还是只是分到只要满足的item就好？剩余的不应用满减
        $newItem = [];
        foreach($cart_items as $item){
            if($this->condition->check($rule, $item) == false){
                $newItem[] = $item;
                continue;
            }else{
                $labour_price = $item['labourPrice'];
                $price = $item['price'];
                $discount = $item['discount'];
                $total_price = bcadd($labour_price,$price);
                $total_price = bcsub($total_price, $discount);
                $percent = bcdiv($total_price,$total_amount);
                $item_order_discount = bcmul($percent, $rule_total_discount);
                
                $item_order_discount = bcdiv( bcmul($rule_total_discount, $total_price), $total_amount);
                
                $item['discount'] = bcadd($discount,$item_order_discount);
                $item['applied_rule_ids'][] = [
                    'rule_id'=>$rule['id'],
                    'discount'=>$item_order_discount,
                    'rule_name'=>$rule['name'],
                    'display_name'=>$rule['display_name'],
                    'extend_name'=>array_get($rule,'extend_name'),
                    'type'=>'coupon',
                ];
                $item['applied_coupon'] = 'yes';
            }
            $newItem[] = $item;
        }
        $this->data['cartItems'] = $newItem;
    }
    
       

}

