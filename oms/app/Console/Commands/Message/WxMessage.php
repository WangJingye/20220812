<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Model\SubscribeShipped;

class WxMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:wxmsg';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'order:wxmsg';

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
        //订阅消息
        $SubscribeShipped = new SubscribeShipped();
        $result = $SubscribeShipped->paidMessage();
	    $this->info(json_encode($result));  
    }
}
