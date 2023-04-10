<?php
namespace App\Service\Rule;

//初始化
class Init extends RuleAbstract
{
    
    //把传入的价格都转换为整数，直接去掉小数点
    //价格=单价*数量
    public function floorPrice(){
        $cart_items = $this->data['cartItems'];
        $new_items = [];
        foreach($cart_items as $item){
//             $item['price'] = bcmul($item['unit_price'],$item['qty']);//价格=单价*数量
            $item['price'] = floor($item['price']);
            $item['labourPrice'] = floor($item['labourPrice']);
            $new_items[] = $item;
        }
        $this->data['cartItems'] = $new_items;
        return $this->data;
    }
    public function setData($data){
        $this->data = $data;
        return $this;
    }

}

