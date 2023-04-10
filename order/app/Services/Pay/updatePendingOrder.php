<?php
namespace App\Services\Pay;

use App\Services\Pay\WxPayService;
use App\Services\Pay\WxMiniAppService;
use App\Services\Pay\AliPayService;
use App\Services\Pay\UnionPayService;
use App\Services\Pay\ChinaPayService;
use Illuminate\Support\Facades\Redis;
use Exception;
use App\Lib\Http;
class updatePendingOrder
{

    public function __construct()
    {
        $this->WxPayService = new WxPayService();
        $this->AliPayService = new  AliPayService();
        $this->UnionPayService = new UnionPayService();
        $this->ChinaPayService = new ChinaPayService();
        $this->pendingOrderKey = env('APP_NAME').'pendingOrder';
    }
    /**
     * [updatePendingOrder 查询未付款的订单-是否已付款  如果已经付款  更新订单]
     * @Author   Peien
     * @DateTime 2020-08-13T10:44:26+0800
     * @return   [type]                   [description]
     */
    public function updatePendingOrder()
    {
        $orderList = Redis::HGETALL($this->pendingOrderKey);
        if(empty($orderList)) return 'true';
        foreach ($orderList as $key => $value) {
            $list[] = json_decode($value,true);
        }
        $lists = [];
        $collection = collect($list);
        $filtered = $collection->filter( function($value, $key)
        {
            if($value['redis_time']+600 < time())
            {
                return $value;
            }
        });
        $lists = $filtered->all();
        $chunk_result = array_chunk($lists, 5);
        foreach ($chunk_result[0] as $key => $value) {
            $Services = $value['type'].'PayService';
            $result = $this->$Services->orderQuery($value);
            if($result['code'] == 1)
            {
                //更新状态为已付款
                 $params = [
                    'id' => $value['order_id'],
                    'pay_order_sn' => $value['out_trade_no'],
                    'type'     => array_search($value['type_num'],$this->getTypeDB()),
                    'tradeType' =>array_search($value['trade_type_num'],$this->getTradeTypeDB()),
                    'payTime'    =>date('Y-m-d H:i:s',$value['redis_time']?? time()),
                ];
                $this->updateOrder($params);
            }
            Redis::HDEL($this->pendingOrderKey,$value['order_sn']);
        }
        return 'true';  
    }


    public  function updateOrder($infoArray)
    {
        $updateData = [
            'pay_order_sn'  => $infoArray['order_sn'] ?? '',
            'payTime'   => $infoArray['payTime'] ?? '',
            'type'      => $infoArray['type'],
            'tradeType' => $infoArray['tradeType'],
            'trade_no'  => $infoArray['trade_no'],
        ];
        Redis::HDEL($this->pendingOrderKey,$infoArray['order_sn']);
        //TODO更新订单状态
        $api = app('ApiRequestInner',['module'=>'oms']);
        $result = $api->request('orders/details','GET',['order_sn'=>'','pay_order_sn' => $infoArray['order_sn']]);
        if(isset($result) && $result['code'] == 0) return 'fail';
        $orderInfo = $result['data'][0];
        \App\Services\WsServices::Notify($orderInfo['order_sn']);
        \Log::info('修改订单参数'.json_encode($updateData));
        //TODO更新订单状态
        $api = app('ApiRequestInner',['module'=>'oms']);
        $successResult = $api->request('pay/success','POST',$updateData);
         \Log::info('修改订单状态返回=',[$successResult]);
        if(isset($successResult) && $successResult['code'] == 0) return 'fail';
        return 'success';
    }

}