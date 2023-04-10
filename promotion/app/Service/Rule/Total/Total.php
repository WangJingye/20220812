<?php
namespace App\Service\Rule\Total;

//计算一些总的数据
// 'total_product_price' => '',//商品总价
// 'total_product_discount' => '',//商品折扣
// 'total_order_discount' =>  '',//满减折扣
// 'total_coupon_discount' => '',//优惠券折扣
// 'total_code_discount' => '',//优惠码折扣
// 'total_member_discount' => '',//会员优惠
// 'total_point_discount' => '',//悦享钱优惠
// 'total_discount' => '',//总折扣价格
// 'total_amount' => '',//合计
//'salesPdt'=>'0.00',//商品折后价，没有下划线的价格（包括商品折扣，会员折扣，悦享钱）

class Total 
{
    
    public function process($data){
        $data = $this->ajustmentDiscount($data);//修正订单级别满减的平分问题
        $data = $this->total_product_price_after_discount($data);
        $data = $this->total_product_price($data);
        $data = $this->total_discount($data);
        $data = $this->total_amount($data);
        $data = $this->total_earn_points($data);
        return $data;
    }
    //商品折扣后价，包括商品折扣，会员折扣，悦享钱折扣
    public function total_product_price_after_discount($data){
        $items = $data['cartItems'];
        $total_product_price_after_discount = 0;
        foreach($items as $item){
            $labour_price = floor($item['labourPrice']);
            $price = floor($item['price']);
            $discount = $item['discount'];
            $product_price_after_discount = bcadd($price,$labour_price);
            if(isset($item['price_after_product_discount']) and $item['price_after_product_discount']){
                $product_price_after_discount = $item['price_after_product_discount'];
            }
            $total_product_price_after_discount = bcadd($total_product_price_after_discount,$product_price_after_discount);
        }
        $data['total_discount']['salesPdt'] = bcadd(floor($total_product_price_after_discount),0);
        return $data;
    }
    public function ajustmentDiscount($data){
        $order_discount = $data['order_discount']??[];
        $rules = [];
        foreach($order_discount as $item){
            $rules[] = $item['rule_id'];
        }
        array_unique($rules);
        foreach($rules as $item){
            $data = $this->ajustmentOneRuleDiscount($data, $item);
        }
        return $data;
    }
    //修正满减平分到item上， 如果所有的item应用这个rule的discount加起来小于order_discount的这个rule，就修正第一个
    public function ajustmentOneRuleDiscount($data,$rule_id){
        $cartItems = $data['cartItems'];
        $order_discount = $data['order_discount'];
        $items_order_total_discount = 0;
        foreach($cartItems as $item){
            if(isset($item['applied_rule_ids'])){
                $applied_rule_ids = $item['applied_rule_ids'];
                foreach($applied_rule_ids as $applied_rule){
                    if($applied_rule['rule_id'] == $rule_id){
                        $items_order_total_discount = bcadd($items_order_total_discount,$applied_rule['discount']);
                    }
                }
            }
        }
        $rule_order_discount = 0;
        foreach($order_discount as $item){
            if($item['rule_id'] == $rule_id){
                $rule_order_discount = bcadd($rule_order_discount,$item['discount']);
            }
        }
        $gap_dicount = bcsub($rule_order_discount,$items_order_total_discount);
        if($gap_dicount > 0){//有差距，补回去，补在第一个
            $new_items = [];
            $k = true;
            foreach($cartItems as $item){
                if(isset($item['applied_rule_ids']) and $k){
                    $applied_rule_ids = $item['applied_rule_ids'];
                    $new_items1 = [];
                    foreach($applied_rule_ids as $applied_rule){
                        if($k and $applied_rule['rule_id'] == $rule_id){
                            $item['discount'] = bcadd($item['discount'],$gap_dicount);
                            $applied_rule['discount'] = bcadd($applied_rule['discount'],$gap_dicount);
                            $new_items1[] = $applied_rule;
                            $k = false;
                        }else{
                            $new_items1[] = $applied_rule;
                        }
                    }
                    $item['applied_rule_ids'] = $new_items1;
                }
                $new_items[] = $item;
            }
            $data['cartItems'] = $new_items;
        }
        return $data;
    }
    //
     public function total_product_price($data){
         $items = $data['cartItems'];
         $total_product_price = 0;
         foreach($items as $item){
             $labour_price = floor($item['labourPrice']);
             $price = floor($item['price']);
             $discount = $item['discount'];
             $product_price = bcadd($price,$labour_price);
             $total_product_price = bcadd($total_product_price,$product_price);
         }
         $data['total_discount']['total_product_price'] = bcadd(($total_product_price),0);
         return $data;
     }
    public function total_discount($data){
        $arr = ['total_product_discount','total_order_discount','total_coupon_discount','total_code_discount','total_member_discount','total_point_discount'];
        foreach ($arr as $dis){
            $data['total_discount'][$dis] = bcadd(($data['total_discount'][$dis]),0);
            $data['total_discount']['total_discount'] = bcadd($data['total_discount']['total_discount'],$data['total_discount'][$dis]);
        }
        $data['total_discount']['total_discount'] = bcadd($data['total_discount']['total_discount'],0);
        return $data;
    }
    public function total_amount($data){
        $data['total_discount']['total_amount'] = bcsub($data['total_discount']['total_product_price'],
                                    $data['total_discount']['total_discount']);
        $data['total_discount']['total_amount'] = bcadd($data['total_discount']['total_amount'],$data['total_discount']['total_ship_fee']);
        return $data;
    }
    public function total_earn_points($data){
        $total_amount = $data['total_discount']['total_amount'];
        $total_earn_points = (int) $total_amount;
        $data['total_discount']['total_earn_points'] = $total_earn_points;
        return $data;
    }
}

