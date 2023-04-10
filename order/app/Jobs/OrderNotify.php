<?php namespace App\Jobs;

use Illuminate\Support\Facades\DB;

class OrderNotify extends Job
{
    protected $allow = [
        'orderCancel',
        'orderCancelBefore',
        'orderShipped',
        'orderReturnAllow',
        'orderRefunded',
    ];
    protected $method;
    protected $entityId;

    /**
     * OrderNotify constructor.
     *
     * @param $method
     * @param $entityId
     * @param int $delay
     */
    public function __construct($method, $entityId, $delay = 0)
    {
        $this->method = $method;
        $this->entityId = $entityId;
        //设置延迟时间(秒)
        if($delay){
            $this->delay($delay);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $error = '';
        if($this->check()){
            $address = DB::table('sales_order_address')->where('parent_id',$this->entityId)->first();
            $order = DB::table('sales_order')->where('entity_id',$this->entityId)->first();
            $mobile = object_get($address,'telephone');
            $status = object_get($order,'status');
            if(($this->method == 'orderCancel' && $status == 'TRADE_CLOSED_BY_TAOBAO')
            ||($this->method == 'orderCancelBefore' && $status == 'WAIT_BUYER_PAY')
            ||($this->method == 'orderShipped' && $status == 'WAIT_BUYER_CONFIRM_GOODS')
            ||($this->method == 'orderReturnAllow' && $status == 'REFUNDING')
            ||($this->method == 'orderRefunded' && $status == 'TRADE_CLOSED')){
                $notifyResult = $this->notify($mobile,$order->increment_id);
            }
        }else{
            $error = 'method不存在';
        }
        $this->log([
            'RequestData'=>[
                'method'=>$this->method,
                'entityId'=>$this->entityId,
                'result'=>$notifyResult??0,
            ],
            'Error'=>$error,
        ]);
    }

    protected function notify($mobile,$orderId){
        /** @var \App\Services\Sms\Alibaba $sms */
        $sms = app('Sms');
        $result = call_user_func([$sms,$this->method],$mobile,compact('orderId'));
        return $result?1:0;
    }

    /**
     * @return bool
     */
    protected function check(){
        if(in_array($this->method,$this->allow)){
            return true;
        }return false;
    }
}
