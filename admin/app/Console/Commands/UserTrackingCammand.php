<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\UserTrackingController;


class UserTrackingCammand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拉取并保存前一天前端埋点UV数据到DB';

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
        $userTracking = new UserTrackingController();
            try{
                $userTracking->DailyUserTracking();
                $userTracking->makeBounceRate();
            }
            catch (\Exception $exception){
                echo $exception;
            }
    }
}
