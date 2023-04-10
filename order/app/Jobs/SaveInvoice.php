<?php namespace App\Jobs;

use Illuminate\Support\Facades\DB;

/**
 * 本地保存客户输入的发票信息
 * Class SaveInvoice
 * @package App\Jobs
 */
class SaveInvoice extends Job
{
    protected $invoice;

    /**
     * SaveInvoice constructor.
     * @param $invoice
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $r = DB::table('ot_invoice')->insert($this->invoice);
        $this->log([
            'RequestData'=>$this->invoice,
            'DBInsert'=>$r?1:0,
        ]);
    }
}
