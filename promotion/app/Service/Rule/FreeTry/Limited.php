<?php
namespace App\Service\Rule\FreeTry;

//如果配置了2个sku, 但是购物车满足了2000，这时需要显示选择2个试用装，虽然满足2000会可以有4个，但是只配置了2个sku
class Limited
{
    public $config = [//从大到小
        '2000'=>4,
        '1000'=>3,
        '0'=>2,
    ];
    //获取限制的数量
    public function getLimitedQty($data){
        $free_try = $data['order_freetry']??[];
        $total_amount = $data['total_discount']['total_amount'];
        if(!$free_try){
            $data['order_freetry_limited_qty'] = 0;
            return $data;
        }
        $rule_config_free_try_skus_qty = 0;
        foreach ($free_try as $item){
            $skus = $item['gift_skus'];
            $skus_arr = explode(',',$skus);
            $qty = is_array($skus_arr)?count($skus_arr):0;
            $rule_config_free_try_skus_qty = $rule_config_free_try_skus_qty + $qty;
        }
        $config = $this->config;
        $limited_qty = 2;
        foreach ($config as $amount=>$qty){
            if($total_amount >= $amount and $rule_config_free_try_skus_qty >= $qty){
                $limited_qty = $qty;
                break;
            }
        }
        $data['order_freetry_limited_qty'] = $limited_qty;
        return $data;
    }
}