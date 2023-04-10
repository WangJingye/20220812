<?php
namespace App\Service\Rule\Code\Product;
use App\Service\Rule\Product\ProductDiscount;
use App\Service\Rule\CrossRuleCheckTrait;
use App\Service\Rule\Code\CodeTrait;
//商品直接折扣
class CodeProductDiscount extends ProductDiscount 
{
    use CrossRuleCheckTrait;
    use CodeTrait;
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
            $this->data['total_discount']['total_code_discount'] = bcadd($this->data['total_discount']['total_code_discount'],
                                        $discount_price);
            $this->data['code_applied'] = '1';
            $item['applied_rule_ids'][] = [
                'rule_id'=>$rule['id'],
                'discount'=>$discount_price,
                'rule_name'=>$rule['name'],
                'display_name'=>$rule['display_name'],
                'extend_name'=>array_get($rule,'extend_name'),
                'type'=>'code',
                'sub_type'=>'code_product_discount',
            ];
            $item['applied_code'] = 'yes';
            $newItem[] = $item;
        }
        $this->data['cartItems'] = $newItem;
    }
    
    
}

