<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Pay\updatePendingOrder;

/**
 * 查询redis 中异常单（请求支付接口后，未能正确回调的订单）
 */
class updateOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:pendingOrder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pendingOrder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new updatePendingOrder())->updatePendingOrder();
    }
}
