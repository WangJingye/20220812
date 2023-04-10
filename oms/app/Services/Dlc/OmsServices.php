<?php namespace App\Services\Dlc;

use App\Exceptions\DlcOmsServiceParamsException;
use App\Model\Order;
use App\Model\Dlc\DlcInvoice;
use App\Jobs\OrderQueued as Queued;
use Illuminate\Contracts\Bus\Dispatcher;

class OmsServices
{
    /**
     * 订单状态同步
     * @param $params
     * @return bool
     * @throws DlcOmsServiceParamsException
     */
    public static function LvmhSiteUpdateOrderStatus($params){
        $order_sn = $params['order_bn'];
        $status = $params['status'];
        if($order_sn && $status){
            $order = Order::query()->where('order_sn',$order_sn)->first();
            if($order){
                switch ($status){
                    case 'synced'://已审核
                        $order->update(['order_status'=>12,'order_state'=>7]);
                        Order::orderLog($order->id,27,'OMS同步已审核');
                        break;
                    case 'shipped'://已发货
                        $express_no = $params['logi_bn'];
                        if(empty($express_no)){
                            throw new DlcOmsServiceParamsException('快递单号为空');
                        }
                        $order->update(['order_status'=>4,'order_state'=>8,'express_no'=>$express_no]);
                        Order::orderLog($order->id,8,'OMS同步已发货');
                        //发送订阅消息
                        (new \App\Model\SubscribeShipped)->shippedMessage($order->id);
                        break;
                    case 'completed'://已完成
                        $order->update(['order_status'=>10,'order_state'=>22]);
                        Order::orderLog($order->id,23,'OMS同步已完成');
                        //发送订阅消息
                        (new \App\Model\SubscribeShipped)->finishMessage($order->id);
                        break;
                    case 'reshipping'://退货申请中(忽略)
//                        $order->update(['order_status'=>8,'order_state'=>17]);
                        Order::orderLog($order->id,17,'OMS同步退货申请中');
                        break;
                    case 'reshipped'://已退货
                        $order->update(['order_status'=>13,'order_state'=>14]);
                        Order::orderLog($order->id,28,'OMS同步已退货');
                        break;
                    case 'refunding'://退款申请中(忽略)
//                        $order->update(['order_status'=>5,'order_state'=>10]);
                        Order::orderLog($order->id,10,'OMS同步退款申请中');
                        break;
                    case 'refunded'://已退款
                        $bill_info = $params['bill_info'];
                        if(empty($bill_info)){
                            throw new DlcOmsServiceParamsException('退款金额为空');
                        }
                        $order->update(['order_status'=>7,'order_state'=>14]);
                        Order::orderLog($order->id,14,'OMS同步已退款');
                        //如果是付邮试用则退回次数
                        if($order->order_type==2){
                            \App\Services\ShipfeeTry\Revert::revertNumber($order_sn);
                        }
                        break;
                    case 'cancel'://已取消
                        $order->update(['order_status'=>11,'order_state'=>23]);
                        Order::orderLog($order->id,24,'OMS同步已取消');
                        //发送订阅消息
                        (new \App\Model\SubscribeShipped)->cancelMessage($order->id);
                        //如果是付邮试用则退回次数
                        if($order->order_type==2){
                            \App\Services\ShipfeeTry\Revert::revertNumber($order_sn);
                        }
                        break;
                    default:
                        throw new DlcOmsServiceParamsException('传入的状态不存在');
                        break;
                }return true;
            }throw new DlcOmsServiceParamsException('订单不存在');
        }throw new DlcOmsServiceParamsException('订单号或状态必传');
    }

    /**
     * 价格同步
     * @param $params
     * @return bool
     * @throws DlcOmsServiceParamsException
     */
    public static function LvmhSiteSyncGoodsPrice($params){
        if($params){
            $resp = app('ApiRequestInner')->request('outward/update/batchPrice','POST',[
                'sku_json'=>$params
            ]);
            if($resp['code']==1){
                return true;
            }throw new DlcOmsServiceParamsException('更新失败');
        }throw new DlcOmsServiceParamsException('入参为空');
    }

    /**
     * 商品库存同步
     * @param $params
     * @param $force
     * @return bool
     * @throws DlcOmsServiceParamsException
     */
    public static function LvmhSiteSyncGoodsStock($params,$force=0){
        if($params){
            $resp = app('ApiRequestInner')->request('outward/update/batchStockFull','POST',[
                'sku_json'=>$params,
                'force' => $force,
            ]);
            if($resp['code']==1){
                //如果有sku的库存是从无到有则发送到货提醒
                $restore_skus = array_get($resp,'data.restore_skus');
                if($restore_skus && is_array($restore_skus) && count($restore_skus)){
                    app(Dispatcher::class)->dispatch(new Queued(
                        'arrivalReminder',
                        ['skus'=>$restore_skus])
                    );
                }
                return true;
            }throw new DlcOmsServiceParamsException('更新失败,'.@$resp['message']);
        }throw new DlcOmsServiceParamsException('入参为空');
    }

    /**
     * @param $params
     * @return bool
     * @throws DlcOmsServiceParamsException
     */
    public static function LvmhSiteSendInvoice($params){
        if($params){
            $order_sn = array_get($params,'order_bn')?:'';
            $invoice_id = array_get($params,'invoice_id')?:'';
            $pdf_url = array_get($params,'pdf_url')?:'';
            $invoice_code = array_get($params,'invoice_code')?:'';
            $invoice_no = array_get($params,'invoice_no')?:'';
            DlcInvoice::query()->updateOrInsert(compact('order_sn'),compact('invoice_id','pdf_url','invoice_code','invoice_no'));
            return true;
        }throw new DlcOmsServiceParamsException('入参为空');
    }

}