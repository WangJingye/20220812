<?php
namespace App\Services\Checkout\Promotion;

//平摊套装的优惠价格到每一个套装的sku上，因为没有把套装的skus传递给promotion，就直接在这里计算了
class Suit
{
    //平摊优惠到套装的skus，根据每个sku的比重
    public function avarageDiscountToSkus($suit_data){
        $suit_discount = $suit_data['discount'];
        $collections = $suit_data['collections'];
        $suit_qty = $suit_data['qty'];
        $suit_price = $suit_data['original_price'];//套装价格，没有乘以qty之前的价格
        $rest_suit_discount = $suit_discount;
        $suit_count = count($collections);//套装里有多少个sku item
        $new_collections = [];
        foreach ($collections as $item){
            $suit_count--;
            $sku_price = $item['original_price'];
            if($suit_price == 0){
                $sku_discount = 0;
            }else{
                $sku_discount = bcdiv(bcmul($sku_price,$suit_discount),$suit_price);
            }
            if($suit_count == 0){//最后一个了
                $sku_discount = $rest_suit_discount;
            }
            $rest_suit_discount = bcsub($rest_suit_discount,$sku_discount);//剩余没有平摊的优惠
            $item['discount'] = $sku_discount;
            $item['paid_amount'] = bcsub($sku_price,$item['discount']);
            $item['suit_count'] = $suit_count;
            $item['rest_suit_discount'] = $rest_suit_discount;
            $new_collections[] = $item;
        }
        return $new_collections;
    }
}