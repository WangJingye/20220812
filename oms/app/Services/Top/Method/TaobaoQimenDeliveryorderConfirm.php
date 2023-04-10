<?php

namespace App\Services\Top\Method;

use App\Model\OrderItem;
use App\Model\Sku;
use App\Model\SubscribeShipped;
use App\Services\Top\TopAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Model\Order;
use App\Support\Sms;
use Illuminate\Support\Facades\Redis;

/**
 * TOP API: taobao.qimen.deliveryorder.confirm request
 *
 * @author auto create
 * @since 1.0, 2018.08.16
 */
class TaobaoQimenDeliveryorderConfirm extends TopAbstract
{

// {"deliveryOrder":{"deliveryOrderCode":"2007162100000088","deliveryOrderId":[],"warehouseCode":"XSL_OK","orderType":"JYCK","status":"DELIVERED","outBizCode":"9B05909387B0492D891DC1613787A0","confirmType":"0","orderConfirmTime":"2020-07-17 15:55:00"},"packages":{"package":{"logisticsCode":"SF","logisticsName":"顺丰速运 ","expressCode":"259047617536","items":{"item":[{"itemCode":"103201","quantity":"1"},{"itemCode":"141570","quantity":"1"},{"itemCode":"152000","quantity":"1"},{"itemCode":"152103","quantity":"1"},{"itemCode":"28-2","quantity":"2"}]}}},"orderLines":{"orderLine":[{"orderLineNo":[],"itemCode":"28-2","inventoryType":"ZP","actualQty":"2"},{"orderLineNo":[],"itemCode":"152000","inventoryType":"ZP","actualQty":"1"},{"orderLineNo":[],"itemCode":"103201","inventoryType":"ZP","actualQty":"1"},{"orderLineNo":[],"itemCode":"152103","inventoryType":"ZP","actualQty":"1"},{"orderLineNo":[],"itemCode":"141570","inventoryType":"ZP","actualQty":"1"}]}}

    public function execute(): array
    {

        $data = $this->request->params;
        Log::info(
            "qmapi.request",
            $data
        );
        $order = Order::where('wms_order', $data['deliveryOrder']['deliveryOrderCode'])->first();
        if(!$order){
            return [50, '订单信息不存在', []];
        }
        if($order['order_status']!=1 && $order['order_status']!=3 && $order['order_status']!=4){
            return [50, '商品未支付，发货失败', []];
        }
        if($order['order_status']!=1 && $order['order_status']!=3 && $order['order_status']!=4){
            return [50, '商品未支付，发货失败', []];
        }
        if($order['order_status']==4){
            return [0, 'success', []];
        }
        Order::where('order_sn', $order['order_sn'])->first()->update(
            [
                'order_status' => 4,//待收货
                'order_state' => 8,//已发货
                'express_no' => $data['packages']['package']['expressCode'],
                'send_at' => $data['deliveryOrder']['orderConfirmTime'],
                'delivery_mode' => $data['packages']['package']['logisticsCode'],
            ]

        );
        if (isset($data['orderLines']['orderLine']['batchs']['batch']['batchCode']) && isset($data['orderLines']['orderLine']['orderLineNo'])) {
            $batch = $data['orderLines']['orderLine']['batchs']['batch']['batchCode'];
            $item = $data['orderLines']['orderLine']['orderLineNo'];
            OrderItem::where('id', $item)->update(
                [
                    'batch' => $batch
                ]
            );
        } else {
            foreach ($data['orderLines']['orderLine'] as $v) {

                if (isset($v['batchs'][0]['batch']['batchCode'])) {
                    $batch = $v['batchs'][0]['batch']['batchCode'];

                    if (is_array($batch)) {
                        $batch = '';
                    }
                    OrderItem::where('id', $v['orderLineNo'])->update(
                        [
                            'batch' => $batch
                        ]
                    );
                }

                if (isset($v['batchs']['batch']['batchCode'])) {
                    $batch = $v['batchs']['batch']['batchCode'];

                    if (is_array($batch)) {
                        $batch = '';
                    }
                    OrderItem::where('id', $v['orderLineNo'])->update(
                        [
                            'batch' => $batch
                        ]
                    );
                }
            }
        }
        $key  = 'deliveryOrder'.date('Ymd');
        Redis::sadd($key,$order['id']);
        Order::orderLog($order['id'], 8, '仓库已发货');
        $sms = new Sms();
        $sub = new SubscribeShipped;
        $sms->send($order['mobile'], 10, $order['contact'], $order['order_sn']);
        $sub->shippedMessage($order['id']);
        return [0, 'success', []];

    }
}