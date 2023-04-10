<?php
namespace App\Services\Checkout\Data;

//套装
class Suit
{
    //查找套装礼盒的skus
    public function parseSuitGiftSkus($suit_products,$selected_skus=[]){
        $suit_skus = [];
        $suit_price = 0;//套装价格
        foreach($suit_products as $item){
            $skus = $item['skus'];
            foreach($skus as $sku){
                if($selected_skus and !in_array($sku['sku_id'],$selected_skus)){//没有选中的
                    continue;
                }
                $sku_price = $sku['ori_price'];
                $is_freebie = $item['is_freebie']??false;
                if($is_freebie){//套装赠品，不计算价格
                    $sku_price = 0;
                }
                $suit_price = bcadd($suit_price,$sku_price);
                $suit_skus[] = [
                    'cart_item_id'=>'',
                    'product_type'=>'',
                    'products'=>[],
                    'qty'=>1,
                    'sku'=>$sku['sku_id'],
                    'name'=>$item['product_name']??'',
                    'pic'=>$sku['kv_images'][0]['url']??'',
                    'short_desc'=>$item['product_name']??'',
                    'collections'=>[],
                    'cats'=>[],//所属类目
                    'original_price'=>$sku_price,
                    'display_type'=>$item['display_type']??'',
                    'spec_desc'=>$sku['spec_desc']??'',
                    'spec_property'=>$sku['spec_property']??'',
                    'revenue_type'=>$sku['revenue_type'],
                    'is_freebie'=>$is_freebie,
                ];
            }
        }
        return [
            'suit_price'=>$suit_price,
            'suit_skus'=>$suit_skus,
            ];
    }
}
