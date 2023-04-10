<?php
namespace App\Service\Rule\Code;
use App\Service\Rule\Gift;
use App\Service\Rule\CrossRuleCheckTrait;
use App\Service\Rule\Code\CodeTrait;
//赠品

class CodeGift extends Gift
{
    use CrossRuleCheckTrait;
    use CodeTrait;
    protected $cartTotalPrice;

    public function collect()
    {
        $this->collectOne($this->rule);
        return $this->data;
    }
    
    public function process($rule)
    {
        $gift = $this->giftValidate($rule);
        if(!$gift){
            return $this->data;
        }
        $this->deal($rule,$gift);
        return $this->data;
    }
    protected function collectOne($rule)
    {
        if($this->codeCheck($rule) == false){
            return $this->data;
        }
        $rule = $this->giftValidate($rule);
        if(!$rule){
            return;
        }
        $this->deal($rule);
    }

    //检测赠品是否有库存，自动过滤没有库存的赠品
    public function giftValidate($rule){
        $gift_stock = $this->data['gift_stock'];
        $rule_gift_sku_arr = explode(',',$rule['gwp_skus']);
        $validate_gift_sku = [];
        foreach($rule_gift_sku_arr as $gift_sku){
//            $stock_qty = $gift_stock[$gift_sku]??0;
//            if($stock_qty){
//                $validate_gift_sku[] = $gift_sku;
//            }
            //赠品无需判断库存
            $validate_gift_sku[] = $gift_sku;
        }
        if(!$validate_gift_sku){
            return false;
        }
        $rule['gwp_skus'] = implode(',',$validate_gift_sku);//有库存的赠品
        return $rule;
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
        $is_special_gift = 0;
        if(strlen(trim($rule['add_sku'])) > 0){//有额外添加sku的不算全场，只针对某个商品做赠品
            $is_special_gift = 1;
        }
        foreach($cart_items as $item){
            if($this->condition->check($rule, $item)){
                $labour_price = $item['labourPrice'];
                $price = $item['price'];
                $discount = $item['discount'];
                $total_price = bcadd($labour_price,$price);
                $total_price = bcsub($total_price, $discount); 
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
        if($rule_total_amount > 0 and $total_amount < $rule_total_amount){
            return false;//有值而且不满足
        }
        if($gift_n > 0 and $total_qty < $gift_n){
            return false;//有值而且不满足
        }
        //计入步长
        \App\Service\Dlc\Rule::makeStep($rule,$total_amount,$total_qty);
        
        $gift_skus = $rule['gwp_skus'];
        $this->data['order_gift'][] = ['rule_id'=>$rule['id'],'rule_name'=>$rule['name'],'display_name'=>$rule['display_name'],
            'gift_skus'=>$gift_skus,];
        $this->data['code_applied'] = '1';
        //平分每个满减到满足条件的item上
        $newItem = [];
        foreach($cart_items as $item){
            if($this->condition->check($rule, $item) == false){
                $newItem[] = $item;
                continue;
            }else{
                if($is_special_gift==1){
                    //如果不是全场则通过全场的标识判断
                    $is_special_gift = $rule['is_whole']?0:1;
                }
                $item['applied_rule_ids'][] = [
                    'rule_id'=>$rule['id'],
                    'gift_skus'=>$gift_skus,
                    'rule_name'=>$rule['name'],
                    'display_name'=>$rule['display_name'],
                    'extend_name'=>array_get($rule,'extend_name'),
                    'is_special_gift'=>$is_special_gift,
                    'type'=>'code',
                    'sub_type'=>'code_gift',
                ];
                $item['applied_code'] = 'yes';
            }
            $newItem[] = $item;
        }
        $this->data['cartItems'] = $newItem;
    }
    
    

}

