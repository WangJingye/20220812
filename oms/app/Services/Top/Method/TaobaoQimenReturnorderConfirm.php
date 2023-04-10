<?php

namespace App\Services\Top\Method;

use App\Model\OrderItem;
use App\Model\Sku;
use App\Services\Top\TopAbstract;
use Exception;
use App\Model\Help;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Model\AfterOrderSale;
use App\Model\Order;
use Illuminate\Support\Facades\Redis;

/**
 * TOP API: taobao.qimen.deliveryorder.confirm request
 *
 * @author auto create
 * @since 1.0, 2018.08.16
 */
class TaobaoQimenReturnorderConfirm extends TopAbstract
{

// {"deliveryOrder":{"deliveryOrderCode":"2007162100000088","deliveryOrderId":[],"warehouseCode":"XSL_OK","orderType":"JYCK","status":"DELIVERED","outBizCode":"9B05909387B0492D891DC1613787A0","confirmType":"0","orderConfirmTime":"2020-07-17 15:55:00"},"packages":{"package":{"logisticsCode":"SF","logisticsName":"顺丰速运 ","expressCode":"259047617536","items":{"item":[{"itemCode":"103201","quantity":"1"},{"itemCode":"141570","quantity":"1"},{"itemCode":"152000","quantity":"1"},{"itemCode":"152103","quantity":"1"},{"itemCode":"28-2","quantity":"2"}]}}},"orderLines":{"orderLine":[{"orderLineNo":[],"itemCode":"28-2","inventoryType":"ZP","actualQty":"2"},{"orderLineNo":[],"itemCode":"152000","inventoryType":"ZP","actualQty":"1"},{"orderLineNo":[],"itemCode":"103201","inventoryType":"ZP","actualQty":"1"},{"orderLineNo":[],"itemCode":"152103","inventoryType":"ZP","actualQty":"1"},{"orderLineNo":[],"itemCode":"141570","inventoryType":"ZP","actualQty":"1"}]}}
    public function execute(): array
    {

        $data = $this->request->params;
        Log::info(
            "qmapi.request",
            $data
        );
        try {
            $after_order = AfterOrderSale::where('after_sale_no', $data['returnOrder']['returnOrderCode'])->first();
            $order_info = Order::where('id', $after_order['order_main_id'])->first();
//            if($order_info['order_status']==5 && $order_info['order_state']== 10){
//                return [0, 'success', []];
//            }
            if(!$after_order){
                return [50, '售后单不存在，请确定后重试', []];
            }

            if($after_order['status']==3){
                return [50, '售后单已取消，请联系客服创建新的售后单', []];
            }
            DB::beginTransaction();


            $damaged = false;
            $stock_sku = [];
            if (isset($data['orderLines']['orderLine']['actualQty']) && isset($data['orderLines']['orderLine']['orderLineNo'])) {
                $item = $data['orderLines']['orderLine']['orderLineNo'];
                if ($data['orderLines']['orderLine']['actualQty'] > 0) {
                    OrderItem::where('id', $item)->update(
                        [
                            'status' => 3,
                            'inventory_type'=>$data['orderLines']['orderLine']['inventoryType']
                        ]
                    );
                    if($data['orderLines']['orderLine']['inventoryType']!='CC'){
                        $stock_sku[] = [$data['orderLines']['orderLine']['itemId'], 1, $order_info['order_sn']];

                    }else{
                        $damaged = true;
                    }
                }

            } else {
                foreach ($data['orderLines']['orderLine'] as $v) {
                    if ($v['actualQty'] > 0) {
                        OrderItem::where('id', $v['orderLineNo'])->update(
                            [
                                'status' => 3,
                                'inventory_type'=>$v['inventoryType']
                            ]
                        );

                        if($v['inventoryType']!='CC'){
                            $stock_sku[] = [$v['itemId'], 1, $order_info['order_sn']];

                        }else{
                            $damaged = true;
                        }


                    }


                }
            }
            if($damaged){
                Order::where('id', $after_order['order_main_id'])->first()->update(
                    [
                        'order_status' => 5,
                        'order_state' => 18,
                    ]

                );
                Order::orderLog($after_order['order_main_id'], 18, '仓库已收到退货商品，存在残次品');
            }else{
                Order::where('id', $after_order['order_main_id'])->first()->update(
                    [
                        'order_status' => 5,
                        'order_state' => 10,
                    ]

                );
                Order::orderLog($after_order['order_main_id'], 10, '仓库已收到退货商品');
            }

            AfterOrderSale::where('id', $after_order['id'])->update(
                [
                    'return_at' => $data['returnOrder']['orderConfirmTime'],
                ]
            );

            DB::commit();
            $key = 'afterOrderReviced' . date('Ymd');
            Redis::sadd($key, $after_order['id']);
            $sku_model = new Sku;
            $sku_model->updateBatchStock(json_encode($stock_sku), $order_info['channel'], 1);
            Help::Log('after_sell:sku:'.$data['returnOrder']['returnOrderCode'],$stock_sku,'wms');
            return [0, 'success', []];
        } catch (Exception $e) {
            DB::rollBack();
            $error = $e->getMessage();
            return [50, $error, []];

        }

    }


}