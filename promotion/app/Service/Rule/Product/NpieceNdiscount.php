<?php
namespace App\Service\Rule\Product;
use App\Service\Rule\RuleAbstract;
//N件N折扣 
//矩阵：不能跟会员折扣叠加，会员折扣指定的情况下，是先计算会员折扣的
class NpieceNdiscount extends RuleAbstract
{
    public $fit_items = [];
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
            if($rule['type'] != 'n_piece_n_discount'){
                continue;
            }
            $this->deal($rule);
        }
    }
    
    public function deal($rule){
        $cartItems = $this->data['cartItems'];
        $fit_qty = 0;
        $in_rule_items = [];//满足rule条件的items
        foreach($cartItems as $item){
            $flag = $this->condition->check($rule, $item);
            if(!$flag){
                continue;
            }
            $fit_qty += (int) $item['qty'];
            $in_rule_items[] = $item;
        }
        $in_rule_items = $this->sortPrice($in_rule_items);//价格高低排序
        $this->fit_items = $in_rule_items;
        $nn_n = explode(',',$rule['nn_n']);
        $nn_discount = explode(',',$rule['nn_discount']);
        //计算最接近的阶梯
        if($fit_qty < min($nn_n)){
            return false;//最小折扣，都没有找到
        }
        $combile_arr = array_combine($nn_n,$nn_discount);
        krsort($combile_arr);
        $rest_fit_qty = $fit_qty;
        $new_fit_items = [];
        foreach ($combile_arr as $rule_nn=>$rule_dis){
            if($rule_nn > $rest_fit_qty){
                continue;
            }
            $new_fit_items[] = $this->dealOneStep($in_rule_items,$rule_dis);
            break;//拿折扣最高的
        }
        
        foreach($new_fit_items as $item){
            $this->setItems($item,$rule);//把满足条件的放到data中返回
        }
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
            $item['price_after_product_discount'] = $this_rule_item['price_after_product_discount'];
            $item['price_after_product_discount_product'] = $this_rule_item['price_after_product_discount'];
            $item['discount'] = $this_rule_item['discount'];            
            $item['applied_rule_ids'][] = [
                'rule_id'=>$rule['id'],
                'discount'=>$this_rule_item['this_rule_discount_amount'],
                'rule_name'=>$rule['name'],
                'display_name'=>$rule['display_name'],
                'extend_name'=>array_get($rule,'extend_name'),
                'type'=>'product_discount',//旧值, discount, 2019/11/19修改过
                'sub_type'=>'n_discount',
            ];
            $item['applied_discount'] = 'yes';
            $new_items[] = $item;
        }
        $this->data['cartItems'] = $new_items;
    }
    //处理一个阶梯，平分每个满减到满足条件的item上
    public function dealOneStep($fit_items,$rule_dis){
        
        $new_items = [];
        foreach($fit_items as $item){//@TODO, 剩余的件数，应该只算剩余的件数折扣
            $labour_price = floor($item['labourPrice']);
            $price = floor($item['price']);
            $discount_price = 0;
            $price_after_product_discount = 0;//商品折扣(商品折扣)之后的价格，（包括商品折扣，会员折扣，悦享钱）
            if($item['priceType'] == 'Y'){//计价，工费
                $item['labour_price_discount'] = $item['labour_price_discount']??0;
                $labour_price = bcsub($labour_price,$item['labour_price_discount']);//折扣后工费
                $labour_price_dicount = bcmul($labour_price,$rule_dis/100);
                $labour_price_dicount = floor($labour_price_dicount);
                $price_after_product_discount = bcadd($price,$labour_price_dicount);
                $discount_price = bcsub($labour_price, $labour_price_dicount);
                $item['labour_price_discount'] = bcadd($item['labour_price_discount'],$discount_price);
            }else{
                $price = bcsub($price,$item['discount']);//折扣后价格
                $price_discount = bcmul($price,$rule_dis/100);
//                $price_discount = floor($price_discount);
                $price_after_product_discount = $price_discount;
                $discount_price = bcsub($price, $price_discount);
            }
            $item['price_after_product_discount'] = $item['price_after_product_discount']??0;
            $item['price_after_product_discount_product'] = $item['price_after_product_discount_product']??0;
            $item['price_after_product_discount'] = bcadd(($price_after_product_discount),$item['price_after_product_discount']);//商品折扣(商品折扣)之后的价格
            $item['price_after_product_discount_product'] = bcadd(($price_after_product_discount),$item['price_after_product_discount_product']);//商品折扣(商品折扣)之后的价格
            $item['discount'] = bcadd($item['discount'], $discount_price);
            
            $item['this_rule_discount_amount'] = $item['this_rule_discount_amount']??0;
            $item['this_rule_discount_amount'] = bcadd($discount_price,$item['this_rule_discount_amount']);
            $this->data['total_discount']['total_product_discount'] = bcadd($this->data['total_discount']['total_product_discount'],
                                $discount_price);
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
    //价格高到低排序
    public function sortPrice($items){
        $sort_key = [];
        foreach($items as $item){
            $sort_key[] = bcadd($item['price'],$item['labourPrice']);
        }
        array_multisort($sort_key, SORT_DESC, $items);
        return $items;
    }
    
}

