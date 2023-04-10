<?php

namespace App\Console\Commands;

use App\Model\Log;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Ring\Exception\ConnectException;
use Illuminate\Console\Command;
use App\Http\Controllers\Api\MiniAppCollectController;
use PHPMailer\PHPMailer\Exception;

class MiniappRetainCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'miniapp:retain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拉取并保存前一天的每日留存数据';

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
        //一次拉取小程序留资的相关数据

        $miniApp = new MiniAppCollectController();
        try {
            $miniApp->getDataDailyRetain();
            $miniApp->getDataDailySummary();
            $miniApp->getDataDailyVisitTrend();
            $miniApp->getDataMonthlyRetain();
            $miniApp->getDataMonthlyVisitTrend();
            $miniApp->getDataUserPortraitDaily();
            $miniApp->getDataUserPortraitWeekly();
            $miniApp->getDataUserPortraitMonthly();
            $miniApp->getDataVisitDistribution();

            $miniApp->getDataVisitPage();
            $miniApp->getDataWeeklyVisitTrend();
            $miniApp->getDataWeeklyRetain();

        } catch (GuzzleException $exception) {
            echo $exception;
        }
    }
}
