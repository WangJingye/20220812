<?php
namespace App\Service\Rule\Point;

//悦享钱，处理用户手动输入使用的悦享钱，把每个商品过一遍
//悦享钱使用是按照每件来的

class ManualPoints extends PointAbstract
{
    public function process($data){
        $total_point = $data['total_points'];
        $items = $data['cartItems'];
        $can_use_point_cids = $this->can_use_points_cids;
        $new_items = [];
        $total_amount = 0;
        $total_can_use_point = 0;
        foreach($items as $item){
            if(isset($item['limited_max_point']) and  $item['usedPoint'] > $item['limited_max_point'] ){
                $item['usedPoint'] = $item['limited_max_point'];//如果用户输入的使用悦享钱比最大能够使用的都大
            }
            if(!$item['usedPoint']){//不指定使用Points
                $new_items[] = $item;
                continue;
            }
            $rest_can_use_point = bcsub($total_point,$total_can_use_point);//剩余能够使用的悦享钱
            if($rest_can_use_point <=0 ){
                $item['usedPoint'] = 0;
                $new_items[] = $item;
                continue;
            }
            $labour_price = floor($item['labourPrice']);
            $price = floor($item['price']);
            $discount = floor($item['discount']);
            $product_price = bcadd($price,$labour_price);
            $can_use_point = 0;
            if(in_array($item['cid'],$can_use_point_cids)){
                if($item['cid'] == $this->charme_string){//手绳
                    $can_use_point = bcmul($product_price,0.99);
                    $can_use_point = ceil($can_use_point);
                    if($can_use_point > $rest_can_use_point){
                        $can_use_point = $rest_can_use_point;
                    }
                }elseif($product_price >=3000){
                    $can_use_point = bcdiv($product_price,2);
                    $can_use_point = ceil($can_use_point);
                    if($can_use_point > $rest_can_use_point){
                        $can_use_point = $rest_can_use_point;
                    }
                }elseif($product_price >= 1000){
                    $can_use_point = bcmul($product_price,0.3);
                    $can_use_point = ceil($can_use_point);
                    if($can_use_point > $rest_can_use_point){
                        $can_use_point = $rest_can_use_point;
                    }
                }
                //使用的悦享钱不能大于剩余可用悦享钱
                if($rest_can_use_point < $can_use_point){
                    $item['usedPoint'] = 0;
                    $new_items[] = $item;
                    continue;
                }
                if($item['usedPoint'] > $can_use_point){
                    $item['usedPoint'] = $can_use_point;
                }
                if($item['usedPoint'] <= 0){
                    $new_items[] = $item;
                    continue;
                }
                $total_can_use_point = $total_can_use_point + (int) $item['usedPoint'];
                
                $price_after_product_discount = bcsub($price,$item['usedPoint']);
                $item['price_after_product_discount'] = $item['price_after_product_discount']??0;
                $item['price_after_product_discount_point'] = $item['price_after_product_discount_point']??0;
                $item['price_after_product_discount'] = bcadd(floor($price_after_product_discount),$item['price_after_product_discount']);//商品折扣(商品折扣)之后的价格
                $item['price_after_product_discount_point'] = bcadd(floor($price_after_product_discount),$item['price_after_product_discount_point']);//商品折扣(商品折扣)之后的价格
                $item['pro_type'] = 'point';
            }
            $data['total_discount']['total_point_discount'] = bcadd($data['total_discount']['total_point_discount'],
                                $item['usedPoint']);
            $new_items[] = $item;
        }
        $data['cartItems'] = $new_items;
        
        return $data;
    }
    
}

