<?php
namespace App\Services\Checkout\ShipfeeTry;

use App\Services\Checkout\Data\Data as normalData;

class Data extends normalData
{
    public function initData(){
        $this->data['ltinfo'] = '';
        $this->data['channel'] = '';
        $this->data['shipping_address_id'] = '';
        $this->data['coupon_id'] = '';
        $this->data['coupon_code'] = '';
        $this->data['used_points'] = '';
        $this->data['shipping_method'] = '';
        $this->data['payment_method'] = '';
        $this->data['trade_type'] = '';
        $this->data['accepted_flag'] = '';
        $this->data['guide'] = '';
        $this->data['invoice'] = [];
        $this->data['invoice']['type'] = '';
        $this->data['invoice']['title'] = '';
        $this->data['invoice']['id'] = '';
        $this->data['invoice']['email'] = '';
        $this->data['card'] = [];
        $this->data['card']['from'] = '';
        $this->data['card']['to'] = '';
        $this->data['card']['content'] = '';
        $this->data['input_selected_redemption_ids'] = '';
        $this->data['ship_fee_try_skus'] = [];
        $this->data['ship_fee_try_campaign_id'] = '';
        $this->data['order_type'] = 2;
        return $this;
    }

    public function mappingInput($input_data)
    {
        parent::mappingInput($input_data);
        $this->data['input_selected_redemption_ids'] = [];
        $this->data['ship_fee_try_skus'] = $input_data['skus']??[];
        $this->data['ship_fee_try_campaign_id'] = $input_data['campaign_id']??'';
        return $this;
    }

    //设置付邮试用的信息
    public function setShipfeeTryInfo($shipfee_try_info){
        $this->data['ship_fee_try_info'] = $shipfee_try_info;
        $this->data['ship_fee_try_shipfee'] = $shipfee_try_info['data'][0]['money'];
        return $this;
    }

    //输入的skus,转换为checkout需要的格式
    public function composeToCheckoutFormat(){
        $main = [];
        $ship_fee_try_skus = $this->data['ship_fee_try_skus'];
        foreach ($ship_fee_try_skus as $item){
            $main[] = [
                'cart_item_id'=>$item['sku'],
                'sku'=>$item['sku'],
                'qty'=>1,
                'product_type'=>1,
                'products'=>[],
            ];
        }
        $goods = [
            [
                'main'=>$main,//主商品
                'gifts'=>[],
            ],
        ];
        $goods_list = [
            'goods'=>$goods,
            'free_try'=>[],
        ];
        $this->data['goods_list'] = $goods_list;
        return $this;
    }

    //设置付邮试用的订单价格
    public function setOrderAmount(){
        $ship_fee = $this->data['ship_fee_try_shipfee'];//付邮活动配置的价格
        $this->data['total'] = [
            'total_product_price'=>0,
            'total_discount'=>0,
            'total_amount'=>$ship_fee,
            'total_ship_fee'=>$ship_fee,
            'total_wrap_fee'=>0,
            'total_used_points'=>0,
        ];
        return $this;
    }
}