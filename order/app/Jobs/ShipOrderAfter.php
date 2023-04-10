<?php namespace App\Jobs;

use App\Repositories\Product\InnerProductRepository;
use Illuminate\Support\Facades\DB;

class ShipOrderAfter extends Job
{
    protected $params;

    /**
     * SaveInvoice constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //发货记录
        (new InnerProductRepository())->shipmentOrderUpdateStock($this->params);
        $this->log([
            'RequestData'=>$this->params,
        ]);
    }
}
