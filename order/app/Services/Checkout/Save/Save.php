<?php
namespace App\Services\Checkout\Save;

use App\Services\Checkout\Save\OmsSave;
//保存订单提交的数据
class Save
{
    public function save($data_obj){
        $data = $data_obj->getData();
        //如果优惠码无效则coupon_code为空 并且将实物券放到applied_rule_ids内
        \App\Services\Dlc\Checkout::filter($data);
        unset($data['product_data']);
        unset($data['promotion_data']);
        $return = (new OmsSave())->save($data);
        return $return;
    }
}
