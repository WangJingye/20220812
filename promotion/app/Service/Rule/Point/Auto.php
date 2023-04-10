<?php
namespace App\Service\Rule\Point;

//积分，整个订单使用，每个商品都可以使用，折扣后再使用积分，优惠平摊到所有商品上
//积分使用是按照整个订单来的，

class Auto extends PointAbstract
{
    //
     public function process($data){
         $used_points = $data['used_points'];
         $total_points = $data['total_points'];
         if($used_points > $total_points){
             $used_points = $total_points;
         }
         $data['total_discount']['total_point_discount'] = bcadd($used_points,0);
         $data['used_points'] = $used_points;
         return $data;
     }
     //平摊到所有商品上，折扣后的价格
    //计算完total之后再平分到每个商品上
     public function averageToItems($data){
         $used_points = $data['used_points'];
         $cart_items = $data['cartItems'];
         $total_amount = bcadd($data['total_discount']['total_amount'],$used_points);//注意这里,之前total已经计算过积分了
         $used_points_items = $this->processItems($cart_items,$used_points,$total_amount);
         $new_items = [];
         foreach($cart_items as $item){
            $cart_item_id = $item['cart_item_id'];
            $item['used_points'] = $used_points_items[$cart_item_id];
            $item['discount'] = bcadd($item['discount'],$item['used_points']);
            $item['origin_final_price'] = bcsub($item['price'],$item['discount']);
             $item['final_price'] = (bcsub($item['price'],$item['discount']));
            $new_items[] = $item;
         }
         $data['cartItems'] = $new_items;
         return $data;
     }

    //处理每个items
    public function processItems($items,$total_point,$total_amount){
        $new_items = [];
        $total_earn_int_points = 0;//去掉小数点之后的总的赚取悦享钱
        //根据比重分配悦享钱到每个items
        foreach($items as $item){
            $price = $item['price'];
            $discount = $item['discount'];
            $item_final_price = bcsub($price,$discount);
            if($total_amount == 0){
                $item_earn_points = 0;
            }else{
                $item_earn_points = bcdiv(bcmul($item_final_price,$total_point), $total_amount);
            }
            $item['earn_raw_points'] = $item_earn_points;//原始赚取的悦享钱
            $item['earn_int_points'] = floor($item['earn_raw_points']);//去掉小数点的悦享钱
            $total_earn_int_points += $item['earn_int_points'];
            $item['earn_diff_points'] = bcsub($item['earn_raw_points'],$item['earn_int_points']) * 1000;//差异的悦享钱
            $new_items[] = $item;
        }
        $total_diff_points = $total_point - $total_earn_int_points;
        $new_items = $this->sortPrice($new_items);
        $result_items = [];
        //把差异部分分配到每个items上
        foreach($new_items as $item){
            $item['earn_points'] = $item['earn_int_points'];
            if($total_diff_points > 0){
                $item['earn_points'] = $item['earn_points'] + 1;
            }
            $total_diff_points--;
            $cart_item_id = $item['cart_item_id'];
            $result_items[$cart_item_id] = $item['earn_points'];
        }
        return $result_items;
    }

    //根据差异部分从高到低排序
    public function sortPrice($items){
        $sort_key = [];
        foreach($items as $item){
            $sort_key[] = $item['earn_diff_points'];
        }
        array_multisort($sort_key, SORT_DESC, $items);
        return $items;
    }

}

