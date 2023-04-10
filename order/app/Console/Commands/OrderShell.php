<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Curl\Curl;
use App\Model\FreeTrial;

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
            $this->line($e->getMessage());
        }$this->line('['.date('Y-m-d H:i:s').']End');
    }

    protected function cachefreetrial(){
        FreeTrial::cacheAllData();
        return;
    }

}
