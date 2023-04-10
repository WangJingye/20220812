<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\Pos\EcToPosFileServices;

class SendSalesToPos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:salesToPos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将已发货的订单信息同步给Pos系统';

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
        try{
            $ecToPos = new EcToPosFileServices();
            //生成订单文件
            $ecToPos->makeSalesData();
        }
        catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
