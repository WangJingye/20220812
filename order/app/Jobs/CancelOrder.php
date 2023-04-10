<?php namespace App\Jobs;

use Illuminate\Support\Facades\DB;

class CancelOrder extends Job
{
    protected $orderId;

    public function __construct($orderId,$delay = null)
    {
        $this->orderId = $orderId;
        //设置延迟时间(秒)
        $this->delay($delay?:env('ORDER_TTL',1800));
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle()
    {
        //查询DB中的订单状态
        $order = DB::table('sales_order')->where('increment_id',$this->orderId)
            ->first();
        if($order && $order->state=='new' && $order->status=='WAIT_BUYER_PAY'){
            //获取client_sn
            $client_sn = "{$order->increment_id}-{$order->serials_number}";
            //如果未支付则查询支付状态
            $paymentOrderInfo = $this->queryPay($client_sn);
            //$status 该状态用于判断是否扫码(暂时不用)
//            $status = object_get($paymentOrderInfo,'biz_response.data.status');
            $order_status = object_get($paymentOrderInfo,'biz_response.data.order_status');
            if($order_status == 'PAID'){
                //订单超时但已支付 则订单状态改为已支付
                $paymentOrderInfoData = object_get($paymentOrderInfo,'biz_response.data');
                if(!$this->payOrder($order->entity_id,$paymentOrderInfoData)){
                    throw new \Exception('订单支付失败[magento]');
                }
            }elseif($order_status == 'CREATED'){
                //如果收钱吧订单已创建则再延迟5分钟
                dispatch(new \App\Jobs\CancelOrder($this->orderId,300));
            }else{
                //订单超时未支付 则取消订单
                if(!$this->cancelOrder($order->entity_id)){
                    throw new \Exception('订单取消失败[magento]');
                }else{
                    //订单取消成功
                    dispatch(new \App\Jobs\CancelOrderAfter($order->entity_id));
                }
            }
        }
        $this->log([
            'OrderId'=>$this->orderId,
        ]);
    }

    /**
     * 调用收钱吧接口查询订单
     * @param $client_sn
     * @return bool|string
     * @throws \Exception
     */
    protected function queryPay($client_sn){
        /** @var \App\Services\UPay\UPay $upay */
        $upay = app('Upay');
        return $upay->query($client_sn);
    }

    /**
     * magento取消订单
     * @param $entity_id
     * @return bool
     */
    protected function cancelOrder($entity_id){
        $magentoApi = app('ApiRequestMagento');
        //调用magento接口取消订单
        $resp = $magentoApi->exec(['url'=>"V1/connext/orders/{$entity_id}/cancel",'method'=>'POST']);
        $resp = json_decode($resp);
        if($resp->code == 0){
            return false;
        }return true;
    }

    /**
     * magento支付订单
     * @param $increment_id
     * @param $data
     * @return bool
     */
    protected function payOrder($increment_id,$data){
        $magentoApi = app('ApiRequestMagento');
        //调用magento接口支付订单
        $resp = $magentoApi->exec(['url'=>"V1/upay/{$increment_id}/notify",'method'=>'POST'],[
            'param'=>$data
        ]);
        $resp = json_decode($resp);
        if($resp->code == 0){
            return false;
        }return true;
    }
}
