<?php
namespace App\Service\Rule\ShipFeeTry;
use App\Service\Rule\CrossRuleCheckTrait;
use App\Service\Rule\RuleAbstract;
//付邮试用，商品价格为0，然后加上运费
//付邮试用商品不参与任何促销折扣

class ShipFeeTry extends RuleAbstract
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
        $this->deal($rule);
    }

    //
    public function deal($rule){
       $cart_items = $this->data['cartItems'];
       $fit_rule_items = [];
        foreach($cart_items as $item){
            if($this->condition->check($rule,$item)){
                $fit_rule_items[] = $item;
            }
        }
        if(!count($fit_rule_items)){
            return false;
        }
        $ship_fee = $rule['ship_fee'];
        $this->data['total_discount']['total_ship_fee'] = $ship_fee;
        $this->data['order_ship_fee_try'][] = ['rule_id'=>$rule['id'],'rule_name'=>$rule['name'],'display_name'=>$rule['display_name'],
            'ship_fee'=>$ship_fee,];
        //平分到每个items
        $newItem = [];
        foreach($fit_rule_items as $item){
            $item['applied_rule_ids'][] = [
                'rule_id'=>$rule['id'],
                'type'=>'ship_fee_try',
                'ship_fee'=>$ship_fee,
                'rule_name'=>$rule['name'],
                'display_name'=>$rule['display_name'],
            ];
            $item['applied_shipfeetry'] = 'yes';
            $newItem[] = $item;
        }
        $this->data['cartItems'] = $newItem;
    }
   

}

