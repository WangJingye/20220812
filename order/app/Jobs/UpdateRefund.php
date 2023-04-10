<?php namespace App\Jobs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * 客户填写退货物流信息后更新退货状态和信息
 * Class UpdateRefund
 * @package App\Jobs
 */
class UpdateRefund extends Job
{
    protected $incrementId;
    protected $data;

    /**
     * SaveInvoice constructor.
     * @param int $incrementId
     * @param array $data
     */
    public function __construct($incrementId,$data)
    {
        $this->incrementId = $incrementId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $updateData = array_merge($this->data,['status'=>'WAIT_SELLER_CONFIRM_GOODS']);
        $result = DB::table('jdp_tb_refund')->where('tid',$this->incrementId)->update($updateData);
        $this->log([
            'RequestIncrement'=>$this->incrementId,
            'RequestData'=>$this->data,
            'Result'=>$result?1:0,
        ]);
        //更新恒康订单表的退货状态(队列)
        $entity_id = DB::table('sales_order')->where('increment_id',$this->incrementId)->value('entity_id');
        $json = json_encode(['method'=>'tbSyncOrder','entity_id'=>$entity_id]);
        Redis::ZADD('OT_Queue:Magento:delay',time(),$json);
    }
}
