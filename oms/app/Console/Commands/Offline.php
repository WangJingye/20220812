<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\OmsDailyOfflineSummary;
use App\Lib\Oss;

/**
 * ╔═════════════╦══════════════════════════════════════════
 * ║File Name    ║   Report.php
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Class Name   ║   Report
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Created Date ║   2020-07-31
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Created By   ║   william.ji@connext.com.cn
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Copy Right   ║   CONNEXT
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Use For      ║   获取token
 * ╚═════════════╩══════════════════════════════════════════
 */
class Offline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offline';

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
     * @throws \Exception
     */
    public function handle()
    {
        $date = date('Ymd', strtotime('-1 day'));
//        $dailyReport = OmsDailyOfflineSummary::where('date', $date)->get()->toArray();
//        if (!empty($dailyReport)) {
//            exit('Daily offline report already run.');
//        }
        $fileName = 'StoreSales' . date('Y-m-d') . '.txt';
        $remote = 'data/upload/' . $fileName;
        $local = storage_path('upload/' . $fileName);
        $ossBack = Oss::getInstance()->getFile($remote, $local);

        if ($ossBack !== true) {
            exit($ossBack);
        }

        $jsonData = file_get_contents($local);

        $offlineData = json_decode($jsonData, true);
        foreach ($offlineData as $dateType => $offlineData) {
            if ($dateType === 'message') {
                continue;
            }
            $tmpInsertData = [];
            $tmpInsertData['date'] = $date;
            $tmpInsertData['totalSalesAmount'] = $offlineData['totalSalesAmount'];
            $tmpInsertData['totalskinCareSales'] = $offlineData['totalskinCareSales'];
            $tmpInsertData['totoalmakeupSales'] = $offlineData['totoalmakeupSales'];
            $tmpInsertData['totalfragranceSales'] = $offlineData['totalfragranceSales'];
            $tmpInsertData['totalhairSales'] = $offlineData['totalhairSales'];
            /*$starSeries = [];
            foreach ($offlineData['starSeries'] as $saleRow) {
                $starSeries[] = [
                    'name' => $saleRow['name'],
                    'amount' => $saleRow['sales_volume'],
                ];
            }
            $tmpInsertData['starSeries'] = json_encode($starSeries);*/
            $byProductSales = [];
            foreach ($offlineData['byProductSales'] as $saleRow) {
                $byProductSales[] = [
                    'name' => $saleRow['name'],
                    'amount' => $saleRow['sales_volume'],
                ];
            }
            $tmpInsertData['byProductSales'] = json_encode($byProductSales);
            $baSales = [];
            foreach ($offlineData['baSales'] as $saleRow) {
                $baSales[] = [
                    'name' => $saleRow['name'],
                    'amount' => $saleRow['sales_volume'],
                ];
            }
            $tmpInsertData['baSales'] = json_encode($baSales);
            $storeSales = [];
            foreach ($offlineData['storeSales'] as $saleRow) {
                $storeSales[] = [
                    'name' => $saleRow['name'],
                    'amount' => $saleRow['sales_volume'],
                ];
            }
            $tmpInsertData['storeSales'] = json_encode($storeSales);
            $citySales = [];
            foreach ($offlineData['citySales'] as $saleRow) {
                $citySales[] = [
                    'name' => $saleRow['name'],
                    'amount' => $saleRow['sales_volume'],
                ];
            }
            $tmpInsertData['citySales'] = json_encode($citySales);

            switch ($dateType) {
                case 'yesterday':
                    $tmpInsertData['type'] = 1;
                    break;
                case 'recentWeek':
                    $tmpInsertData['type'] = 2;
                    break;
                case 'recentMonth':
                    $tmpInsertData['type'] = 3;
                    break;
                default:
                    continue 2;
            }

            OmsDailyOfflineSummary::updateOrCreate(
                ['date' => $date, 'type' => $tmpInsertData['type']],
                $tmpInsertData
            );
        }
    }
}
