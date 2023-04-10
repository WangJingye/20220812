<?php
namespace App\Service\Rule\FreeTry;
use App\Service\Rule\CrossRuleCheckTrait;
use App\Service\Rule\RuleAbstract;
//使用装
class FreeTry extends RuleAbstract
{
    use CrossRuleCheckTrait;
    protected $cartTotalPrice;

    public function collect()
    {
        $this->collectOne($this->rule);
        return $this->data;
    }
    
    protected function collectOne($rule)
    {
//         $gift = $this->giftValidate($rule);
//         if(!$gift){
//             return;
//         }
        $this->deal($rule);
    }
    
    // 每个规则应用一遍每一个商品
    protected function collectByPriority()
    {
        $rules = $this->data['rules'];
        foreach ($rules as $rule) {
            if($rule['type'] != 'gift'){
                continue;
            }
            $gift = $this->giftValidate($rule);
            if(!$gift){
                continue;
            }
            $this->deal($rule,$gift);
        }
    }
    //
    public function giftValidate($rule){
        $gifts = $this->data['gifts'];
        foreach($gifts as $g){
            if($g['id'] == $rule['gift_id']){
                if($g['used_qty'] >= $g['qty']){
                    return false;
                }
                return $g;
            }
        }
        return false;
    }
    //
    public function deal($rule){
        $cartItems = $this->data['cartItems'];
        //是否满足满xxxx，计算总价，需要计算之前折扣后的价格
        $cart_items = $this->data['cartItems'];
        $total_amount = 0;
        $total_qty = 0;
        $cids = explode(',',$rule['cids']);
        $excludes_style_number = explode(',',$rule['exclude_sku']);
        $includes_style_number = explode(',',$rule['add_sku']);
        foreach($cart_items as $item){
            if($this->condition->check($rule,$item)){
                $labour_price = $item['labourPrice'];
                $price = $item['price'];
                $discount = $item['discount'];
                $used_points = $item['usedPoint'];
                $total_price = bcadd($labour_price,$price);
                $total_price = bcsub($total_price, $discount);
                $total_price = bcsub($total_price, $used_points); //减去悦享钱，悦享钱可以跟赠品叠加
                $total_amount = bcadd($total_amount,$total_price);
                $total_qty += (int)$item['qty'];
            }
        }
        if(!$total_amount and !$total_qty){
            return false;
        }
        //计算最接近的阶梯
        $rule_total_amount = $rule['gift_amount'];
        $gift_n = $rule['gift_n'];
        if($total_amount == 0){
            return false;//无商品
        }
        if($rule_total_amount > 0 and $total_amount < $rule_total_amount){
            return false;//有值而且不满足
        }
        if($gift_n > 0 and $total_qty < $gift_n){
            return false;//有值而且不满足
        }
                
        $gift_skus = $rule['gwp_skus'];
        $this->data['order_freetry'][] = ['rule_id'=>$rule['id'],'rule_name'=>$rule['name'],'display_name'=>$rule['display_name'],
            'gift_skus'=>$gift_skus,];
        //平分每个满减到满足条件的item上
        $newItem = [];
        foreach($cart_items as $item){
            if($this->condition->check($rule, $item) == false){
                $newItem[] = $item;
                continue;
            }else{
                $item['applied_rule_ids'][] = [
                    'rule_id'=>$rule['id'],
                    'type'=>'freetry',
                    'gift_skus'=>$gift_skus,
                    'rule_name'=>$rule['name'],
                    'display_name'=>$rule['display_name'],
                    'extend_name'=>array_get($rule,'extend_name'),
                ];
                $item['applied_freetry'] = 'yes';
            }
            $newItem[] = $item;
        }
        $this->data['cartItems'] = $newItem;
    }
   

}

