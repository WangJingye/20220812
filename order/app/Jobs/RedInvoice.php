<?php namespace App\Jobs;

use Illuminate\Support\Facades\DB;

/**
 * 调用收钱吧接口红冲发票
 * Class MakeInvoice
 * @package App\Jobs
 */
class RedInvoice extends Job
{
    protected $task_sn;

    /**
     * RedInvoice constructor.
     * @param $task_sn
     */
    public function __construct($task_sn)
    {
        $this->task_sn = $task_sn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var \App\Services\UPay\Invoice $uPayInvoice */
        $uPayInvoice = app('UpayInvoice');
        //API红冲发票
        $result = $uPayInvoice->redInvoice($this->task_sn)?'red_send':'red_send_fail';
        $r = DB::table('ot_invoice')->where('task_sn',$this->task_sn)
            ->update(['status'=>$result]);
        $this->log([
            'TaskSn'=>$this->task_sn,
            'InvoiceSend'=>$result,
            'DBUpdate'=>$r?1:0,
        ]);
    }
}
