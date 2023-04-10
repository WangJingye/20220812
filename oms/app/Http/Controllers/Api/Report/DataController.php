<?php

namespace App\Http\Controllers\Api\Report;

use App\Model\OmsDailyOfflineSummary;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;
use App\Model\OmsDailySummary;
use App\Model\OmsDailyItemsSummary;
use Illuminate\Support\Facades\DB;
use App\Tools\Http;
use App\Tools\Tools;

class DataController extends ApiController
{

    public function getOrderReportData(Request $request)
    {
        //1商城概览2订单概览3销售数据
        $viewType = $request->viewType;
        //1昨天2近七天3近一个月4累计
        $dateType = $request->dateType;
        if ($dateType === '4' && $viewType !== '1') {
            return $this->error('参数异常');
        }
        $return = [];
        //起始时间
        $endDate = date('Y-m-d', strtotime('-1 day'));
        switch ($viewType) {
            case '1':
                switch ($dateType) {
                    case '1':
                        $sRange = '-1 day';
                        break;
                    case '2':
                        $sRange = '-7 day';
                        break;
                    case '3':
                        $sRange = '-30 day';
                        break;
                    case '4':
                        break;
                    default:
                        return $this->error('参数异常');
                }
                if (isset($sRange)) {
                    $fromDate = date('Y-m-d', strtotime($sRange));
                    $data = OmsDailySummary::where('date', '>=', $fromDate)->where('date', '<=', $endDate)->first(
                        [
                            DB::raw('SUM(totalSalesAmount) as totalSalesAmount'),
                            DB::raw('SUM(totalSalesCount) as totalSalesCount'),
                            DB::raw('SUM(totalUserCount) as totalUserCount')
                        ]
                    )->toArray();
                } else {
                    $data = OmsDailySummary::where('date', '<=', $endDate)->first(
                        [
                            DB::raw('SUM(totalSalesAmount) as totalSalesAmount'),
                            DB::raw('SUM(totalSalesCount) as totalSalesCount'),
                            DB::raw('SUM(totalUserCount) as totalUserCount')
                        ]
                    )->toArray();
                }
                $return['totalSalesAmount'] = number_format($data['totalSalesAmount']);
                $return['totalSalesCount'] = number_format($data['totalSalesCount']);
                $return['totalUserCount'] = number_format($data['totalUserCount']);
                $return['averageSalesAmount'] = $return['totalSalesCount'] != "0" ? bcdiv($data['totalSalesAmount'] , $data['totalSalesCount']) : '0';
                break;
            case '2':
                switch ($dateType) {
                    case '1':
                        $sRange = '-1 day';
                        break;
                    case '2':
                        $sRange = '-7 day';
                        break;
                    case '3':
                        $sRange = '-30 day';
                        break;
                    default:
                        return $this->error('参数异常');
                }
                $fromDate = date('Y-m-d', strtotime($sRange));
                $data = OmsDailySummary::where('date', '>=', $fromDate)->where('date', '<=', $endDate)->first(
                    [
                        DB::raw('SUM(totalCreatedSalesCount) as totalCreatedSalesCount'),
                        DB::raw('SUM(totalPaidSalesCount) as totalPaidSalesCount'),
                        DB::raw('SUM(totalShippedSalesCount) as totalShippedSalesCount'),
                        DB::raw('SUM(totalReceivedSalesCount) as totalReceivedSalesCount'),
                        DB::raw('SUM(totalAfterSalesCount) as totalAfterSalesCount')
                    ]
                )->toArray();
                $return['totalCreatedSalesCount'] = number_format($data['totalCreatedSalesCount']);
                $return['totalPaidSalesCount'] = number_format($data['totalPaidSalesCount']);
                $return['totalShippedSalesCount'] = number_format($data['totalShippedSalesCount']);
                $return['totalReceivedSalesCount'] = number_format($data['totalReceivedSalesCount']);
                $return['totalAfterSalesCount'] = number_format($data['totalAfterSalesCount']);
                break;
            case '3':
                switch ($dateType) {
                    case '1':
                        $day = 3;
                        $sRange = '-1 day';
                        $salesData = OmsDailySummary::where('date', date('Y-m-d', strtotime('-1 day')))->get(['yesOrderAccount', 'yesUserSecPurchase'])->toArray();
                        $return['orderAccount'] = $salesData[0]['yesOrderAccount'];
                        $return['userSecPurchase'] = $salesData[0]['yesUserSecPurchase'];
                        break;
                    case '2':
                        $day = 2;
                        $sRange = '-7 day';
                        $salesData = OmsDailySummary::where('date', date('Y-m-d', strtotime('-1 day')))->get(['weekOrderAccount', 'weekUserSecPurchase'])->toArray();
                        $return['orderAccount'] = $salesData[0]['weekOrderAccount'];
                        $return['userSecPurchase'] = $salesData[0]['weekUserSecPurchase'];
                        break;
                    case '3':
                        $day = 1;
                        $sRange = '-30 day';
                        $salesData = OmsDailySummary::where('date', date('Y-m-d', strtotime('-1 day')))->get(['monthOrderAccount', 'monthUserSecPurchase'])->toArray();
                        $return['orderAccount'] = $salesData[0]['monthOrderAccount'];
                        $return['userSecPurchase'] = $salesData[0]['monthUserSecPurchase'];
                        break;
                    default:
                        return $this->error('参数异常');
                }
                $fromDate = date('Y-m-d', strtotime($sRange));
                $data = OmsDailySummary::where('date', '>=', $fromDate)->where('date', '<=', $endDate)->first(
                    [
                        DB::raw('SUM(skinCare) as skinCare'),
                        DB::raw('SUM(makeUp) as makeUp'),
                        DB::raw('SUM(fragrance) as fragrance'),
                        DB::raw('SUM(hair) as hair'),
                        DB::raw('SUM(other) as other')
                    ]
                )->toArray();
                $onlineStarProdSalesData = OmsDailyItemsSummary::where('date', '>=', $fromDate)->where('date', '<=', $endDate)->where('scene', 'online')
                    ->where('type', 'star')
                    ->select('product_name as name', DB::raw('SUM(qty) as qty'), DB::raw('SUM(amount) as rawAmount'))
                    ->groupBy('product_id', 'product_name')
                    ->orderBy('rawAmount', 'desc')
                    ->take(5)
                    ->get()->toArray();
                foreach ($onlineStarProdSalesData as $onlineStarProdSalesKey => $onlineStarProdSalesRow) {
                    $onlineStarProdSalesData[$onlineStarProdSalesKey]['amount'] = number_format($onlineStarProdSalesRow['rawAmount']);
                    unset($onlineStarProdSalesData[$onlineStarProdSalesKey]['rawAmount']);
                }
                $onlineNormalProdSalesData = OmsDailyItemsSummary::where('date', '>=', $fromDate)->where('date', '<=', $endDate)
                    ->where('type', 'normal')
                    ->select('product_name as name', DB::raw('SUM(qty) as qty'), DB::raw('SUM(amount) as rawAmount'))
                    ->groupBy('product_id', 'product_name')
                    ->orderBy('rawAmount', 'desc')
                    ->take(5)
                    ->get()->toArray();
                foreach ($onlineNormalProdSalesData as $onlineNormalProdSalesKey => $onlineNormalProdSalesRow) {
                    $onlineNormalProdSalesData[$onlineNormalProdSalesKey]['amount'] = number_format($onlineNormalProdSalesRow['rawAmount']);
                    unset($onlineNormalProdSalesData[$onlineNormalProdSalesKey]['rawAmount']);
                }
                $o2oStarProdSalesData = OmsDailyItemsSummary::where('date', '>=', $fromDate)->where('date', '<=', $endDate)->where('scene', 'o2o')
                    ->where('type', 'star')
                    ->select('product_name as name', DB::raw('SUM(qty) as qty'), DB::raw('SUM(amount) as rawAmount'))
                    ->groupBy('product_id', 'product_name')
                    ->orderBy('rawAmount', 'desc')
                    ->take(5)
                    ->get()->toArray();
                foreach ($o2oStarProdSalesData as $o2oStarProdSalesKey => $o2oStarProdSalesRow) {
                    $o2oStarProdSalesData[$o2oStarProdSalesKey]['amount'] = number_format($o2oStarProdSalesRow['rawAmount']);
                    unset($o2oStarProdSalesData[$o2oStarProdSalesKey]['rawAmount']);
                }
                $list = [];
                $list[] = ['cat_name' => '护肤', 'amount' => number_format($data['skinCare']), 'rawAmount' => $data['skinCare']];
                $list[] = ['cat_name' => '彩妆', 'amount' => number_format($data['makeUp']), 'rawAmount' => $data['makeUp']];
                $list[] = ['cat_name' => '香水', 'amount' => number_format($data['fragrance']), 'rawAmount' => $data['fragrance']];
                $list[] = ['cat_name' => '头发', 'amount' => number_format($data['hair']), 'rawAmount' => $data['hair']];
                $list[] = ['cat_name' => '其他', 'amount' => number_format($data['other']), 'rawAmount' => $data['other']];
                $return['onlineTotalSales']['total'] = number_format(array_sum(array_column($list, 'rawAmount')));
                $return['onlineTotalSales']['list'] = $list;
                $return['onlineStarTotalSales'] = $onlineStarProdSalesData;
                $return['onlineNormalTotalSales'] = $onlineNormalProdSalesData;
                $return['o2oStarTotalSales'] = $o2oStarProdSalesData;
                //线下数据
                $offlineData = OmsDailyOfflineSummary::where('date', $endDate)->where('type', $dateType)->get()->toArray();
                if (!empty($offlineData)) {
                    $offlineList = [];
                    $offlineList[] = ['cat_name' => '护肤', 'amount' => number_format($offlineData[0]['totalskinCareSales']), 'rawAmount' => $offlineData[0]['totalskinCareSales']];
                    $offlineList[] = ['cat_name' => '彩妆', 'amount' => number_format($offlineData[0]['totoalmakeupSales']), 'rawAmount' => $offlineData[0]['totoalmakeupSales']];
                    $offlineList[] = ['cat_name' => '香水', 'amount' => number_format($offlineData[0]['totalfragranceSales']), 'rawAmount' => $offlineData[0]['totalfragranceSales']];
                    $offlineList[] = ['cat_name' => '头发', 'amount' => number_format($offlineData[0]['totalhairSales']), 'rawAmount' => $offlineData[0]['totalhairSales']];
                    $return['offlineTotalSales']['total'] = number_format($offlineData[0]['totalSalesAmount']);
                    $return['offlineTotalSales']['list'] = $offlineList;
//                    $return['offlineStarTotalSales'] = Tools::formatNumberInArray(array_slice(json_decode($offlineData[0]['starSeries'], true), 0, 5), ['amount']);
                    $return['offlineGoodsListTop'] = Tools::formatNumberInArray(array_slice(json_decode($offlineData[0]['byProductSales'], true), 0, 5), ['amount']);
                    $return['offlineGuidesListTop'] = Tools::formatNumberInArray(array_slice(json_decode($offlineData[0]['baSales'], true), 0, 5), ['amount']);
                    $return['offlineStoresListTop'] = Tools::formatNumberInArray(array_slice(json_decode($offlineData[0]['storeSales'], true), 0, 5), ['amount']);
                    $return['offlineCitysListTop'] = Tools::formatNumberInArray(array_slice(json_decode($offlineData[0]['citySales'], true), 0, 5), ['amount']);
                } else {
                    $offlineList = [];
                    $offlineList[] = ['cat_name' => '护肤', 'amount' => '0', 'rawAmount' => 0];
                    $offlineList[] = ['cat_name' => '彩妆', 'amount' => '0', 'rawAmount' => 0];
                    $offlineList[] = ['cat_name' => '香水', 'amount' => '0', 'rawAmount' => 0];
                    $offlineList[] = ['cat_name' => '头发', 'amount' => '0', 'rawAmount' => 0];
                    $return['offlineTotalSales']['total'] = '0';
                    $return['offlineTotalSales']['list'] = $offlineList;
//                    $return['offlineStarTotalSales'] = [];
                    $return['offlineGoodsListTop'] = [];
                    $return['offlineGuidesListTop'] = [];
                    $return['offlineStoresListTop'] = [];
                    $return['offlineCitysListTop'] = [];
                }
                //o2o数据
                $o2oBack = Http::httpRequest(['type' => $day], config('api.map')['store/dashBoardNeedData'], 'GET');
                if (!empty($o2oBack)) {
                    if (empty($o2oBack['data']['typesListTop']['list'])) {
                        $list = [];
                        $list[] = ['cat_name' => '护肤', 'amount' => '0', 'rawAmount' => 0];
                        $list[] = ['cat_name' => '彩妆', 'amount' => '0', 'rawAmount' => 0];
                        $list[] = ['cat_name' => '香水', 'amount' => '0', 'rawAmount' => 0];
                        $list[] = ['cat_name' => '头发', 'amount' => '0', 'rawAmount' => 0];
                        $list[] = ['cat_name' => '其他', 'amount' => '0', 'rawAmount' => 0];
                        $o2oBack['data']['typesListTop']['list'] = $list;
                    } else {
                        foreach ($o2oBack['data']['typesListTop']['list'] as $tkey => $tval) {
                            $o2oBack['data']['typesListTop']['list'][$tkey]['rawAmount'] = $tval['amount'] ?? 0;
                            $o2oBack['data']['typesListTop']['list'][$tkey]['amount'] = number_format($o2oBack['data']['typesListTop']['list'][$tkey]['rawAmount']);
                        }
                    }
                    $o2oBack['data']['typesListTop']['total'] = number_format($o2oBack['data']['typesListTop']['total']);
                    $return = array_merge($return, $o2oBack['data']);
                }
                break;
        }

        return $this->success('成功', $return);
    }
}
