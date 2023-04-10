<?php
namespace App\Service\Rule\Code\Order;
use App\Service\Rule\Order\FullReductionOfOrder;
use App\Service\Rule\CrossRuleCheckTrait;
use App\Service\Rule\Code\CodeTrait;
//满减 

class CodeFullReductionOfOrder extends FullReductionOfOrder
{
    use CrossRuleCheckTrait;
    use CodeTrait;
    protected $cartTotalPrice;
    
    public function collect()
    {
        if($this->codeCheck($this->rule) == false){
            return $this->data;
        }
        $this->deal($this->rule);
        return $this->data;
    }
    
    public function process($rule)
    {
        $this->deal($rule);
        return $this->data;
    }
    //满减
    public function deal($rule){
        $cartItems = $this->data['cartItems'];
        if($rule['type'] != 'code_full_reduction_of_order'){
            return false;
        }
        //是否满足满xxxx，计算总价，需要计算之前折扣后的价格
        $cart_items = $this->data['cartItems'];
        $total_amount = 0;
        $cids = explode(',',$rule['cids']);
        $excludes_style_number = explode(',',$rule['exclude_sku']);
        $includes_style_number = explode(',',$rule['add_sku']);
        $in_rule_items = [];//满足rule条件的items
        foreach($cart_items as $item){
            if($this->condition->check($rule, $item)){
                $labour_price = $item['labourPrice'];
                $price = $item['price'];
                $discount = $item['discount'];
                $total_price = bcadd($labour_price,$price);
                $total_price = bcsub($total_price, $discount);
                $total_amount = bcadd($total_amount,$total_price);
                $in_rule_items[] = $item;
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
        
        $combile_arr = array_combine($rule_total_amount,$rule_total_discount);
        arsort($combile_arr);
        $rest_total_amount = $total_amount;
        foreach ($combile_arr as $rule_amt=>$rule_dis){
            if($rule_amt > $rest_total_amount){
                continue;
            }
            $in_rule_items = $this->dealOneStep($in_rule_items,$total_amount,$rest_total_amount,$rule, $rule_amt, $rule_dis);
            $rest_total_amount = bcsub($rest_total_amount,$rule_amt);
            break;//拿最高的
        }
        $this->setItems($in_rule_items,$rule);//把满足条件的放到data中返回
    }
    //符合的items，放到data['items']中
    public function setItems($items,$rule){
        $cart_items = $this->data['cartItems'];
        $new_items = [];
        foreach($cart_items as $item){
            $this_rule_item = $this->getItemByCartItemsId($item['cart_item_id'], $items);
            if(!$this_rule_item){
                $new_items[] = $item;
                continue;
            }
            $item['discount'] = $this_rule_item['discount'];           
            $item['applied_rule_ids'][] = [
                'rule_id'=>$rule['id'],
                'discount'=>$this_rule_item['this_rule_discount_amount'],
                'rule_name'=>$rule['name'],
                'display_name'=>$rule['display_name'],
                'extend_name'=>array_get($rule,'extend_name'),
                'type'=>'code',//满减
                'sub_type'=>'code_cut',
            ];
            $item['applied_code'] = 'yes';
            $new_items[] = $item;
        }
        $this->data['cartItems'] = $new_items;
    }
    //处理一个阶梯，平分每个满减到满足条件的item上
    public function dealOneStep($in_rule_items,$total_amount,$rest_total_amount,$rule,$rule_total_amount,$rule_total_discount){
        $this->data['order_discount'][] = ['rule_id'=>$rule['id'],'rule_name'=>$rule['name'],'display_name'=>$rule['display_name'],'discount'=>$rule_total_discount];
        $this->data['total_discount']['total_code_discount'] = bcadd($this->data['total_discount']['total_code_discount'],
                                    $rule_total_discount);
        $this->data['code_applied'] = '1';
        
        $new_items = [];
        foreach($in_rule_items as $item){
            $labour_price = $item['labourPrice'];
            $price = $item['price'];
            $discount = $item['discount'];
            $total_price = bcadd($labour_price,$price);
            $total_price = bcsub($total_price, $discount);
            
            $item_order_discount = bcdiv( bcmul($rule_total_discount, $total_price), $total_amount);
            
            $item['discount'] = bcadd($discount,$item_order_discount);
            $item['this_rule_discount_amount'] = $item['this_rule_discount_amount']??0;
            $item['this_rule_discount_amount'] = bcadd($item_order_discount,$item['this_rule_discount_amount']);
            $new_items[] = $item;
        }
        return $new_items;
    }
    public function getItemByCartItemsId($cart_item_id,$items){
        foreach($items as $item){
            if($item['cart_item_id'] == $cart_item_id){
                return $item;
            }
        }
        return false;
    }
    
    

}

