<?php
namespace App\Service\Rule\Point;
use App\Service\Rule\Point\Earn\Item;
//悦享钱，可以赚取的悦享钱
//

class Earn extends PointAbstract
{
    public $earn_data = [];
    public function process($data){
        $data = $this->total($data);
        $data = $this->item($data);
        return $data;
    }
    //处理每个item的赚取悦享钱
    public function item($data){
        $earn_points_item = (new Item($this->earn_data))->process();
        $cart_items = $data['cartItems'];
        $new_items = [];
        foreach($cart_items as $item){
            $cart_item_id = $item['cart_item_id'];
            $item['earn_points'] = $earn_points_item[$cart_item_id]??0;
            $new_items[] = $item;
        }
        $data['cartItems'] = $new_items;
        return $data; 
    }
    //折扣后的价格，整个订单可以赚取的悦享钱
     public function total($data){
         $items = $data['cartItems'];
         $cids_fixed = $this->can_ean_points_cids_fixed;
         $cids_non_fixed = $this->can_ean_points_cids_non_fixed;
         $new_items = [];
         $total_amount_fixed = 0;
         $total_amount_non_fixed = 0;
         foreach($items as $item){
             $labour_price = floor($item['labourPrice']);
             $price = floor($item['price']);
             $discount = $item['discount'];
             $used_point = $item['usedPoint'];
             $product_price = bcadd($price,$labour_price);
             $final_price = bcsub($product_price,$discount);
             $final_price = bcsub($final_price,$used_point);
             $item['final_price'] = $final_price;
             if(in_array($item['cid'],$cids_fixed)){
                 $total_amount_fixed = $total_amount_fixed + $final_price;
                 $this->earn_data['fix_items'][] = $item;
             }
             if(in_array($item['cid'],$cids_non_fixed)){
                 $total_amount_non_fixed = $total_amount_non_fixed + $final_price;
                 $this->earn_data['non_fix_items'][] = $item;
             }
             $new_items[] = $item;
         }
         $data['cartItems'] = $new_items;
         if($total_amount_fixed >= 1000){
             $earn_point_fixed_step = bcdiv($total_amount_fixed,1000);
             $earn_point_fixed_step = floor($earn_point_fixed_step);
             $earn_point_fixed = bcmul($earn_point_fixed_step,50);
             $data['points']['earn'] = $earn_point_fixed;
             $this->earn_data['fix_total_point'] = $earn_point_fixed;
             $this->earn_data['fix_total_amount'] = $total_amount_fixed;
         }
         if($total_amount_non_fixed >= 1000){
             $earn_point_non_fixed_step = bcdiv($total_amount_non_fixed,1000);
             $earn_point_non_fixed_step = floor($earn_point_non_fixed_step);
             $earn_point_non_fixed = bcmul($earn_point_non_fixed_step,10);
             $this->earn_data['non_fix_total_point'] = $earn_point_non_fixed;
             $this->earn_data['non_fix_total_amount'] = $total_amount_non_fixed;
             $data['points']['earn'] = bcadd($data['points']['earn'], $earn_point_non_fixed);
         }
         return $data;
     }

}

