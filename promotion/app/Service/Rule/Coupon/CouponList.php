<?php
namespace App\Service\Rule\Coupon;
use App\Service\Rule\RuleAbstract;

//优惠券 列表， 满足当前购物车条件的所有的优惠券
//随单礼券，没有使用条件，都可以使用的
class CouponList extends RuleAbstract
{

    protected $cartTotalPrice;

    public function setData($data){
        $this->data = $data;
        return $this;
    }

    public function collect()
    {
        $this->collectByPriority();
        return $this->data;
    }

    // 每个规则应用一遍每一个商品
    protected function collectByPriority()
    {
        $member_coupon_list = $this->data['member_coupon_list']??'';
        $member_coupon_list_arr = explode(',',$member_coupon_list);
        $rules = $this->data['rules'];
        foreach ($rules as $rule) {
            if(!in_array($rule['type'],['coupon','coupon_discount','product_coupon'])){
                continue;
            }
            $rule_id = $rule['id'];
            if(!in_array($rule_id,$member_coupon_list_arr)){//不在用户优惠券列表里
                continue;
            }
            if($rule['type'] == 'product_coupon'){//随单礼券，没有使用条件
                $this->data['coupon_list'][] = [
                    'rule_id'=>$rule['id'],
                    'coupon_discount'=>$rule['total_discount'],
                    'coupon_amount'=>$rule['total_amount'],
                    'start_time'=>$rule['start_time'],
                    'end_time'=>$rule['end_time'],
                ] ;
                continue;
            }elseif($rule['type'] == 'coupon'){
                $this->deal($rule);
            }elseif($rule['type'] == 'coupon_discount'){
                $this->deal_coupon_discount($rule);
            }
        }
    }
    //
    public function couponValidate($rule){
        $rest_stock = $rule['coupon_stock'] - $rule['coupon_stock_used'];
        if($rest_stock >0){
            return true;
        }
        return false;
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
        $cids = $rule['cids']?explode(',',$rule['cids']):[];
        $excludes_style_number = explode(',',$rule['exclude_sku']);
        $includes_style_number = explode(',',$rule['add_sku']);
        foreach($cart_items as $item){
            $item_style_number = $item['styleNumber'];
            $item_cids = $item['mid']?explode(',',$item['mid']):[];
            if(in_array($item_style_number, $excludes_style_number)){
                continue;
            }
            if(
                array_intersect($item_cids,$cids) or
                in_array($item_style_number,$includes_style_number) or
                array_get($rule,'is_whole')==1 //增加全场
            ){
                $labour_price = $item['labourPrice'];
                $price = $item['price'];
                $discount = $item['discount'];
                $total_price = bcadd($labour_price,$price);
                $total_price = bcsub($total_price, $discount);
                $total_amount = bcadd($total_amount,$total_price);
            }
        }
        //计算最接近的阶梯
        $rule_total_amount = explode(',',$rule['total_amount']);
        $rule_total_discount = explode(',',$rule['total_discount']);
        if($total_amount < min($rule_total_amount)){
            return false;
        }
        //满足条件
        $this->data['coupon_list'][] = [
            'rule_id'=>$rule['id'],
            'coupon_discount'=>$rule['total_discount'],
            'coupon_amount'=>$rule['total_amount'],
            'start_time'=>$rule['start_time'],
            'end_time'=>$rule['end_time'],
        ] ;
    }

    public function deal_coupon_discount($rule):void{
        $cart_items = $this->data['cartItems'];
        $cids = explode(',',$rule['cids']);
        $excludes_style_number = explode(',',$rule['exclude_sku']);
        $includes_style_number = explode(',',$rule['add_sku']);
        $flag = 0;
        foreach($cart_items as $item){
            $item_style_number = $item['styleNumber'];
            $item_cids = explode(',',$item['mid']);
            if(in_array($item_style_number, $excludes_style_number)){
                continue;
            }
            if(
                array_intersect($item_cids,$cids) or
                in_array($item_style_number,$includes_style_number) or
                array_get($rule,'is_whole')==1 //增加全场
            ){
                $flag = 1;
                break;
            }
        }
        if($flag){
            //满足条件
            $this->data['coupon_list'][] = [
                'rule_id'=>$rule['id'],
                'start_time'=>$rule['start_time'],
                'end_time'=>$rule['end_time'],
            ] ;
        }
    }

}

