<?php

namespace App\Services\Top\Method;

use App\Services\Top\TopAbstract;
use App\Model\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TOP API: TaobaoQimenOrderprocessReport
 *
 * @author auto create
 * @since 1.0, 2018.08.16
 */
class TaobaoQimenOrderprocessReport extends TopAbstract
{

//[2020-07-17 16:16:52]
//Promotion.INFO: qmapi.TaobaoQimenOrderprocess
//{"order":{"OrderCode":"2007162100000088","OrderId":[]},"process":{"processStatus":"SIGN","operateTime":"2020-07-17 16:16:01.917","operateInfo":"本人签收 签收成功","operatorName":"快递接口","remark":[]}}
//DELIVERED=已发货;
//SIGN=签收;
//REFUSE=买家拒签;
//TMSCANCELED=快递拦截;
//FULFILLED=收货完成;
    public function execute(): array
    {
        $data = $this->request->params;
        Log::info(
            "qmapi.TaobaoQimenOrderprocess",
            $data
        );

        $order = Order::where('wms_order',$data['order']['OrderCode'])->first();
        if(!$order){
            Log::info(
                "qmapi.TaobaoQimenOrderprocess.error",
                $data
            );
            return [50, '订单不存在', []];
        }
        if($data['process']['processStatus']=='SIGN'){
            if($order['order_status']!=4){
                return [50, '订单不是待发货状态，不能签收', []];
            }

            Order::where('order_sn',$order['order_sn'])->first()->update(
                [
                    'order_status'=>9,
                    'order_state'=>16,
                    'received_at'=>$data['process']['operateTime'],
                ]

            );
            Order::orderLog($order['id'], 16, '用户已签收');
            return [0, 'success', []];
        }

        if($data['process']['processStatus']=='REFUSE'){
            //若用户拒收 则判断用户受否货到付款

            if($order->payment_type==5){
                Order::where('order_sn',$order['order_sn'])->first()->update(
                    [
                        'order_status'=>8,
                        'order_state'=>24,
                        'received_at'=>$data['process']['operateTime'],
                    ]

                );
                Order::orderLog($order['id'], 21, '货到付款，用户拒签');
            }else{
                //如果不是货到付款 则需要客服退款
                Order::where('order_sn',$order['order_sn'])->first()->update(
                    [
                        'order_status'=>8,
                        'order_state'=>21,

                    ]

                );
                Order::orderLog($order['id'], 21, '线上支付，用户拒签');

            }

        }

        if($data['process']['processStatus']=='FULFILLED'){
            //若仓库已收货

                //若货到付款 用户拒收
                if($order->payment_type==5 && $order->order_state==24){
                    Order::where('order_sn',$order['order_sn'])->first()->update(
                        [
                            'order_status'=>11,//已关闭
                            'order_state'=>10,//货物已回仓
                        ]

                    );
                    Order::orderLog($order['id'], 10, '仓库已收到拒收回退商品');

                }else{
                    Order::where('order_sn', $order['order_sn'])->first()->update(
                        [
                            'order_status' => 5,
                            'order_state' => 10,
                        ]

                    );
                    Order::orderLog($order['id'], 10, '仓库已收到退货商品');
                }




        }

        if($data['process']['processStatus']=='TMSCANCELED'){
            //快递拦截
            if($order->payment_type==5) {
                Order::where('order_sn', $order['order_sn'])->first()->update(
                    [
                        'order_status' => 8,
                        'order_state' => 24,
                    ]

                );
                Order::orderLog($order['id'], 24, '货到付款,快递已拦截');
            }else{
                Order::where('order_sn', $order['order_sn'])->first()->update(
                    [
                        'order_status' => 8,
                        'order_state' => 24,
                    ]

                );
                Order::orderLog($order['id'], 24, '线上付款 快递已拦截');
            }

        }


        return [0, 'success', []];

    }
}
