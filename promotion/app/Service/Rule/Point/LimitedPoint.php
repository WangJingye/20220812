<?php
namespace App\Service\Rule\Point;

//悦享钱，每个item最大使用的悦享钱
//悦享钱使用是按照每件来的

class LimitedPoint extends PointAbstract
{
    public function process($data){
        $items = $data['cartItems'];
        $can_use_point_cids = $this->can_use_points_cids;
        $new_items = [];
        $order_can_used_max_points = 0;
        foreach($items as $item){
            $labour_price = floor($item['labourPrice']);
            $price = floor($item['price']);
            $discount = $item['discount'];
            $product_price = bcadd($price,$labour_price);
            $can_use_point = 0;
            if(in_array($item['cid'],$can_use_point_cids)){
                if($item['cid'] == $this->charme_string){//手绳
                    $can_use_point = bcmul($product_price,0.99);
                    $can_use_point = ceil($can_use_point);
                }elseif($product_price >=3000){
                    $can_use_point = bcdiv($product_price,2);
                    $can_use_point = ceil($can_use_point);
                }elseif($product_price >= 1000){
                    $can_use_point = bcmul($product_price,0.3);
                    $can_use_point = ceil($can_use_point);
                }
            }
            $item['limited_max_point'] = $can_use_point;//最大使用悦享钱
            $order_can_used_max_points = $order_can_used_max_points + $can_use_point;
            $new_items[] = $item;
        }
        $data['order_can_used_max_points'] = $order_can_used_max_points;
        $data['cartItems'] = $new_items;
        return $data;
    }
    
}

