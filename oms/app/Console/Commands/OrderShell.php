<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Model\Order;

class OrderShell extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:shell {option} {params?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     */
    public function handle()
    {
        try {
            $option = $this->argument('option');
            $params = $this->argument('params');
            call_user_func_array([__CLASS__, $option], [$params]);
            $this->line('['.date('Y-m-d H:i:s').']Successful');
        } catch (\Exception $e) {
            $this->line($e->getMessage().',line:'.$e->getLine());
        }$this->line('['.date('Y-m-d H:i:s').']End');
    }

    /**
     * 已发货状态N天变为已完成
     */
    public function orderend(){
        $end_date = date("Y-m-d H:i:s",strtotime("-1 week"));
        //已发货->已完成
        Order::query()->where('created_at','<',$end_date)
            ->where('order_status',4)->get()->each(function($item){
                $item->update(['order_status'=>10,'order_state'=>22]);
                Order::orderLog($item->id,23,'OMS同步已完成');
                //发送订阅消息
                (new \App\Model\SubscribeShipped)->finishMessage($item->id);
            });
    }

}
