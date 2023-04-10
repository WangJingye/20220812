<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Service\OmsService;

class UpdateOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:updateStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'updateStatus';

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
        $OmsService = new OmsService();
        $info = $OmsService->updateOrderStatus();
        $this->info(json_encode($info));
    }
}
