<?php namespace App\Jobs;

use App\Repositories\OrderRepository;
use App\Repositories\Product\InnerProductRepository;
use Illuminate\Support\Facades\DB;

/**
 * 调用收钱吧接口开发票
 * Class MakeInvoice
 * @package App\Jobs
 */
class MakeInvoice extends Job
{
    protected $increment_id;

    /**
     * SaveInvoice constructor.
     * @param $increment_id
     */
    public function __construct($increment_id)
    {
        $this->increment_id = $increment_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $invoice = OrderRepository::getInvoice($this->increment_id);
        if($invoice){
            $order = OrderRepository::getDetail($this->increment_id);
            //获取订单中的商品
            $items = OrderRepository::getItems($order->entity_id)->keyBy('sku');
            $skus = $items->keys()->toArray();
            //获取商品属性
            $items_attr = collect((new InnerProductRepository())->getSkus($skus))->pluck('ot_product_type_value','sku');
            //组装属性
            $order->items = $items->reduce(function ($result, $item) use($items_attr){
                $item->product_type = $items_attr->get($item->sku);
                $result[] = $item;
                return $result;
            });
            /** @var \App\Services\UPay\Invoice $uPayInvoice */
            $uPayInvoice = app('UpayInvoice');
            //API开发票
            $task_sn = $uPayInvoice->makeInvoice($invoice,$order);
            $result = $task_sn?'send':'send_fail';
            $r = DB::table('ot_invoice')->where('tid',$this->increment_id)
                ->update(['status'=>$result,'task_sn'=>$task_sn]);
            $this->log([
                'IncrementId'=>$this->increment_id,
                'InvoiceSend'=>$result,
                'DBUpdate'=>$r?1:0,
            ]);
        }else{
            $this->log([
                'IncrementId'=>$this->increment_id,
                'InvoiceSend'=>'no invoice',
            ]);
        }
    }
}
