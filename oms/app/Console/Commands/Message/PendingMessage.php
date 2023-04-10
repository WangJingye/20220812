<?php

namespace App\Console\Commands\Message;
use Illuminate\Console\Command;
use App\Model\SubscribeShipped;

class PendingMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pending:msg';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pendiMsg';

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
        $this->info('未付款msg触发时间'.date('m-d h:i:s'));
        $result = $SubscribeShipped->paidMessage();
        $this->info(json_encode($result));  
    }
}
