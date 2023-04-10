<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Report\OrderService;
use App\Model\OmsDailySummary;
use App\Model\OmsDailyItemsSummary;

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
class Report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report';

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
        $dailyReport = OmsDailySummary::where('date', $date)->get()->toArray();
        if (!empty($dailyReport)) {
            //exit('Daily report already run.');
        }

        $dailyData = [];
        //总的销售额
        $dailyData['totalSalesAmount'] = OrderService::totalSalesAmountDaily();
//        $dailyData['format.totalSalesAmount'] = number_format($dailyData['totalSalesAmount'], 2);
        //总的成交订单量
        $dailyData['totalSalesCount'] = OrderService::totalSalesCountDaily();
//        $dailyData['format.totalSalesCount'] = number_format($dailyData['totalSalesCount']);
        //总的购买人数
        $dailyData['totalUserCount'] = OrderService::totalUserCountDaily();
//        $dailyData['format.totalUserCount'] = number_format($dailyData['totalUserCount']);
        //客单价
        $dailyData['averageSalesAmount'] = $dailyData['totalSalesCount'] === 0 ? 0 : $dailyData['totalSalesAmount'] / $dailyData['totalSalesCount'];
//        $dailyData['format.averageSalesAmount'] = number_format($dailyData['averageSalesAmount']);

        //创建的订单量
        $dailyData['totalCreatedSalesCount'] = OrderService::totalCreatedSalesCountDaily();
//        $dailyData['format.totalCreatedSalesCount'] = number_format($dailyData['totalCreatedSalesCount']);
        //支付订单量
        $dailyData['totalPaidSalesCount'] = OrderService::totalPaidSalesCountDaily();
//        $dailyData['format.totalPaidSalesCount'] = number_format($dailyData['totalPaidSalesCount']);
        //发货订单量
        $dailyData['totalShippedSalesCount'] = OrderService::totalShippedSalesCountDaily();
//        $dailyData['format.totalShippedSalesCount'] = number_format($dailyData['totalShippedSalesCount']);
        //签收订单量
        $dailyData['totalReceivedSalesCount'] = OrderService::totalReceivedSalesCountDaily();
//        $dailyData['format.totalReceivedSalesCount'] = number_format($dailyData['totalReceivedSalesCount']);
        //售后订单量
        $dailyData['totalAfterSalesCount'] = OrderService::totalAfterSalesCountCountDaily();
//        $dailyData['format.totalAfterSalesCount'] = number_format($dailyData['totalAfterSalesCount']);

        //各分类销量
        $totalAfterSalesCount = OrderService::deviseSalesCountCountDaily();
        $dailyData['skinCare'] = $totalAfterSalesCount['cateSalesAmount']['SKINCARE']['amount'];
        $dailyData['makeUp'] = $totalAfterSalesCount['cateSalesAmount']['Makeup']['amount'];
        $dailyData['fragrance'] = $totalAfterSalesCount['cateSalesAmount']['fragrance']['amount'];
        $dailyData['hair'] = $totalAfterSalesCount['cateSalesAmount']['hair rituel']['amount'];
        $dailyData['other'] = $totalAfterSalesCount['cateSalesAmount']['other']['amount'];
        $cateSpecSalesAmount = $totalAfterSalesCount['cateSpecSalesAmount'];
        $dailyProdSales = !empty($totalAfterSalesCount['prodSales']) ? array_values($totalAfterSalesCount['prodSales']) : [];

        $totalO2OAfterSalesCount = OrderService::deviseO2OSalesCountCountDaily();
        $o2oCateSpecSalesAmount = $totalO2OAfterSalesCount['cateSpecSalesAmount'];
        //新老客访问订单占比率
        $dailyData['yesOrderAccount'] = OrderService::orderAccountDaily('yesterday');
        $dailyData['weekOrderAccount'] = OrderService::orderAccountDaily('week');
        $dailyData['monthOrderAccount'] = OrderService::orderAccountDaily('month');
        //老客的复购率
        $dailyData['yesUserSecPurchase'] = OrderService::userSecPurchaseDaily('yesterday');
        $dailyData['weekUserSecPurchase'] = OrderService::userSecPurchaseDaily('week');
        $dailyData['monthUserSecPurchase'] = OrderService::userSecPurchaseDaily('month');

        OmsDailySummary::updateOrCreate(
            ['date' => $date],
            $dailyData
        );

        if ($dailyProdSales) {
            foreach ($dailyProdSales as $prodSale) {
                OmsDailyItemsSummary::updateOrCreate(
                    ['date' => $date, 'type' => $prodSale['type'], 'product_id' => $prodSale['product_id']],
                    $prodSale
                );
            }
        }

        foreach ($cateSpecSalesAmount as $cateSpecSalesAmountRow) {
            if (!empty($cateSpecSalesAmountRow['details'])) {
                foreach ($cateSpecSalesAmountRow['details'] as $detail) {
                    OmsDailyItemsSummary::updateOrCreate(
                        ['date' => $date, 'type' => $detail['type'], 'scene' => $detail['scene'], 'product_id' => $detail['product_id']],
                        $detail
                    );
                }
            }
        }

        foreach ($o2oCateSpecSalesAmount as $o2oCateSpecSalesAmountRow) {
            if (!empty($o2oCateSpecSalesAmountRow['details'])) {
                foreach ($o2oCateSpecSalesAmountRow['details'] as $detail) {
                    OmsDailyItemsSummary::updateOrCreate(
                        ['date' => $date, 'type' => $detail['type'], 'scene' => $detail['scene'], 'product_id' => $detail['product_id']],
                        $detail
                    );
                }
            }
        }
    }
}
