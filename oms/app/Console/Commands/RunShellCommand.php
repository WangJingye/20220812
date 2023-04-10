<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\OmsOrderStatus;
use Illuminate\Support\Facades\Redis;

class RunShellCommand extends Command
{
    protected $signature = 'run:shell {option}';

    protected $description = 'Run shell';

    public function __construct(){
        parent::__construct();
    }

    public function handle()
    {
        try{
            $option = $this->argument('option');
            call_user_func([__CLASS__,$option]);
            $this->line('All done');
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }

    /**
     * 生成目录
     */
    protected function cachestatus(){

        $states_info = OmsOrderStatus::query()->pluck('state_name', 'state')->toarray();
        $status_info = OmsOrderStatus::query()->pluck('status_name', 'status')->toarray();
        $status_json = [$states_info,$status_info];
        Redis::set('oms_status',json_encode($status_json));
    }

}