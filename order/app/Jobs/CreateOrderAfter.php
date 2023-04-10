<?php namespace App\Jobs;

use App\Repositories\Product\InnerProductRepository;
use Illuminate\Support\Facades\DB;

class CreateOrderAfter extends Job
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
        $result = DB::table('sales_order_item')->where('order_id',$this->entityId)->pluck('qty_ordered','product_id');
        $params = [];
        foreach($result as $key=>$value){
            $params[$key] = intval($value);
        }
        if($params){
            //记录
            (new InnerProductRepository())->saveSkuSales($params);
            (new InnerProductRepository())->placeOrderUpdateStock($params);
        }
        $this->log([
            'RequestData'=>$this->entityId,
            'Skus'=>$params,
        ]);
    }
}
