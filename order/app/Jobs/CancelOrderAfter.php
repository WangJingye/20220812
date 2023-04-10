<?php namespace App\Jobs;

use App\Repositories\Product\InnerProductRepository;
use Illuminate\Support\Facades\DB;

class CancelOrderAfter extends Job
{
    protected $entityId;

    /**
     * SaveInvoice constructor.
     * @param $entityId
     */
    public function __construct($entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //取消通知
        dispatch(new \App\Jobs\OrderNotify('orderCancel',$this->entityId));
        //取消记录
        $result = DB::table('sales_order_item')->where('order_id',$this->entityId)->pluck('qty_canceled','product_id');
        $params = [];
        foreach($result as $key=>$value){
            $params[$key] = intval($value);
        }
        if($params){
            //记录
            (new InnerProductRepository())->cancelOrderUpdateStock($params);
        }
        $this->log([
            'RequestData'=>$this->entityId,
            'Skus'=>$params,
        ]);
    }
}
