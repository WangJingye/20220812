<?php
namespace App\Services\Checkout\Save;

//调用oms接口，下单。扣库存和扣积分都放在oms，取消订单也放在oms
class OmsSave
{
    public $oms_create_order_api = 'oms/orderInput';
    public function save($data){
        $api = app('ApiRequestInner',['module'=>'oms']);
        try{
            $return = $api->request($this->oms_create_order_api,'POST',$data);
            return $this->parseOmsReturn($return);
        }catch (\Exception $e){
        }
    }
    //解析oms返回的错误信息
    public function parseOmsReturn($return){
        return $return;
    }
}
