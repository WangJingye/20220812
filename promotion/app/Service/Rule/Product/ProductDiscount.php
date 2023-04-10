<?php
namespace App\Service\Rule\Product;
use App\Service\Rule\RuleAbstract;
//商品直接折扣
//矩阵：不能跟会员折扣叠加，会员折扣指定的情况下，是先计算会员折扣的
class ProductDiscount extends RuleAbstract
{

    public function collect()
    {
        $this->deal($this->rule);
        return $this->data;
    }
    
    // 每个规则应用一遍每一个商品
    protected function collectByPriority()
    {
        $rules = $this->data['rules'];
        foreach ($rules as $rule) {
            if($rule['type'] != 'product_discount'){
                continue;
            }
            $this->deal($rule);
        }
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
            $price_after_product_discount = 0;//商品折扣(商品折扣)之后的价格，（包括商品折扣，会员折扣，悦享钱）
            if($item['priceType'] == 'Y'){//计价，工费
                $item['labour_price_discount'] = $item['labour_price_discount']??0;
                $labour_price = bcsub($labour_price,$item['labour_price_discount']);//折扣后工费
                $labour_price_dicount = bcmul($labour_price,$discount/100);
                $labour_price_dicount = floor($labour_price_dicount);
                $price_after_product_discount = bcadd($price,$labour_price_dicount);
                $discount_price = bcsub($labour_price, $labour_price_dicount);
                $item['labour_price_discount'] = bcadd($item['labour_price_discount'],$discount_price);
            }else{
                $price = bcsub($price,$item['discount']);//折扣后价格
                $price_discount = bcmul($price,$discount/100);
//                $price_discount = floor($price_discount);
                $price_after_product_discount = $price_discount;
                $discount_price = bcsub($price, $price_discount);
            }
            $item['unit_price_after_discount'] = (bcmul($item['unit_price'],$discount/100));
            $item['price_after_product_discount'] = $item['price_after_product_discount']??0;
            $item['price_after_product_discount_product'] = $item['price_after_product_discount_product']??0;
            $item['price_after_product_discount'] = bcadd(($price_after_product_discount),$item['price_after_product_discount']);//商品折扣(商品折扣)之后的价格
            $item['price_after_product_discount_product'] = bcadd(($price_after_product_discount),$item['price_after_product_discount_product']);//商品折扣(商品折扣)之后的价格
            
            $item['discount'] = bcadd($item['discount'], $discount_price);
            $this->data['total_discount']['total_product_discount'] = bcadd($this->data['total_discount']['total_product_discount'],
                                                            $discount_price);
            $item['applied_rule_ids'][] = [
                'rule_id'=>$rule['id'],
                'discount'=>$discount_price,
                'rule_name'=>$rule['name'],
                'display_name'=>$rule['display_name'],
                'extend_name'=>array_get($rule,'extend_name'),
                'type'=>'product_discount',
                'sub_type'=>'discount',
            ];
            $item['applied_discount'] = 'yes';
            $newItem[] = $item;
        }
        $this->data['cartItems'] = $newItem;
    }
    
    
   
}

