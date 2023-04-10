<?php
/**
 *  ===========================================
 *  File Name   AuthApiBase.php
 *  Class Name  AuthApiBase
 *  Date        2020-07-28 14:13
 *  Created by  William Ji
 *  ===========================================
 **/

namespace App\Services\Report;

use App\Model\Order;
use App\Model\AfterOrderSale;
use App\Lib\Http;
use Illuminate\Support\Facades\Redis;
use Mockery\Exception;

class OrderService
{
    /**
     * 获取日期范围
     * @param string $range
     * @return array
     */
    public static function lastDayDate($range = '-1 day')
    {
        $lastDay = strtotime($range);
        $return = [];
        $return['date'] = date('Y-m-d', $lastDay);
        $return['start_at'] = date('Y-m-d 00:00:00', $lastDay);
        $return['end_at'] = date('Y-m-d 23:59:59', $lastDay);

        return $return;
    }

    /**
     * 总的销售额-每日
     * @return false|float
     */
    public static function totalSalesAmountDaily()
    {
        //\DB::connection()->enableQueryLog();#开启执行日志
        $lastDate = self::lastDayDate();
        //$totalSalesAmount = Order::where('transaction_date', $lastDate['date'])->sum('total_amount');
        $totalSalesAmount =Order::orWhere(function ($query) use ($lastDate)  {
            $query->where('payment_type', '<>', 5)->where('order_type', '<>' ,2)->where('transaction_date', $lastDate['date']);
        })->orwhere(function($query1) use ($lastDate){
            $query1->where('payment_type', 5)->whereDate('send_at', $lastDate['date'])->where('order_type', '<>' ,2);
        })->sum('total_product_price');
        //print_r(\DB::getQueryLog()); //获取查询语句、参数和执行时间
        return round($totalSalesAmount, 2);
    }

    /**
     * 总的成交订单量-每日
     * @return mixed
     */
    public static function totalSalesCountDaily()
    {
        $lastDate = self::lastDayDate();
        return Order::orWhere(function ($query) use ($lastDate)  {
            $query->where('payment_type', '<>', 5)->where('order_type', '<>' ,2)->where('transaction_date', $lastDate['date']);
        })->orwhere(function($query1) use ($lastDate){
            $query1->where('payment_type', 5)->whereDate('send_at', $lastDate['date'])->where('order_type', '<>' ,2);
        })->count('*');
    }

    /**
     * 总的购买人数-每日
     * @return array
     */
    public static function totalUserCountDaily()
    {
        $lastDate = self::lastDayDate();
        return Order::orWhere(function ($query) use ($lastDate)  {
            $query->where('payment_type', '<>', 5)->where('order_type', '<>' ,2)->where('transaction_date', $lastDate['date']);
        })->orwhere(function($query1) use ($lastDate){
            $query1->where('payment_type', 5)->whereDate('send_at', $lastDate['date'])->where('order_type', '<>' ,2);
        })->distinct('user_id')->count('user_id');
        //return Order::where('transaction_date', $lastDate['date'])->distinct('user_id')->where('order_type', '<>', 2)->count('user_id');
    }

    /**
     * 创建订单量-每日
     * @return mixed
     */
    public static function totalCreatedSalesCountDaily()
    {
        $lastDate = self::lastDayDate();
        return Order::where('created_at', '>=', $lastDate['start_at'])->where('created_at', '<=', $lastDate['end_at'])->where('order_type', '<>', 2)->count('*');
    }

    /**
     * 支付订单量-每日
     * @return mixed
     */
    public static function totalPaidSalesCountDaily()
    {
        $lastDate = self::lastDayDate();
        return Order::orWhere(function ($query) use ($lastDate)  {
            $query->where('payment_type', '<>', 5)->where('order_type', '<>' ,2)->where('transaction_date', $lastDate['date']);
        })->orwhere(function($query1) use ($lastDate){
            $query1->where('payment_type', 5)->whereDate('send_at', $lastDate['date'])->where('order_type', '<>' ,2);
        })->count('*');
        //return Order::where('transaction_date', $lastDate['date'])->where('order_type', '<>', 2)->count('*');
    }

    /**
     * 发货订单量-每日
     * @return mixed
     */
    public static function totalShippedSalesCountDaily()
    {
        $lastDate = self::lastDayDate();
        return Order::where('send_at', '>=', $lastDate['start_at'])->where('send_at', '<=', $lastDate['end_at'])->where('order_type', '<>', 2)->count('*');
    }

    /**
     * 签收订单量-每日
     * @return mixed
     */
    public static function totalReceivedSalesCountDaily()
    {
        $lastDate = self::lastDayDate();
        return Order::where('received_at', '>=', $lastDate['start_at'])->where('received_at', '<=', $lastDate['end_at'])->where('order_type', '<>', 2)->count('*');
    }

    /**
     * 售后订单量-每日
     * @return mixed
     */
    public static function totalAfterSalesCountCountDaily()
    {
        $lastDate = self::lastDayDate();
        return AfterOrderSale::where('created_at', '>=', $lastDate['start_at'])->where('created_at', '<=', $lastDate['end_at'])->count('*');
    }

    /**
     * 线上商城-各分类销量-每日
     * @return mixed
     */
    public static function deviseSalesCountCountDaily()
    {
        $cateSalesAmount = [
            'SKINCARE' => [
                'name' => '护肤',
                'amount' => 0
            ],
            'Makeup' => [
                'name' => '彩妆',
                'amount' => 0
            ],
            'fragrance' => [
                'name' => '香水',
                'amount' => 0
            ],
            'hair rituel' => [
                'name' => '头发',
                'amount' => 0
            ],
            'other' => [
                'name' => '其他',
                'amount' => 0
            ],
        ];

        $transCate = [
            '护肤' => 'SKINCARE',
            '彩妆' => 'Makeup',
            '香水' => 'fragrance',
            '头发' => 'hair rituel',
        ];

        $cateSpecSalesAmount = [
            [
                'id' => 13,
                'name' => '明星系列',
                'type' => 'star',
                'details' => []
            ],
        ];
        $lastDate = self::lastDayDate();

        $start = 0;
        $limit = 100;

        $Http = new Http();

        $skuData = [];
        $prodSales = [];
        while (true) {
            //获取当日订单的商品数据
            //$orderList = Order::where('transaction_date', $lastDate['date'])->where('order_type', '<>', 2)->
            $orderList = Order::orWhere(function ($query) use ($lastDate)  {
                $query->where('payment_type', '<>', 5)->where('order_type', '<>' ,2)->where('transaction_date', $lastDate['date']);
            })->orwhere(function($query1) use ($lastDate){
                $query1->where('payment_type', 5)->whereDate('send_at', $lastDate['date'])->where('order_type', '<>' ,2);
            })->with(['orderDataItem' => function ($query) {
                $query->select('order_main_id', 'sku', 'qty', 'product_amount_total')
                    ->where('is_gift', 0)
                    ->where('is_free', 0)
                    ->where(function ($mq) {
                        $mq->where('type', 1)->orWhere('type', 3)->orWhere(function ($fq) {
                            $fq->where('type', 2)->where('collections', '');
                        });
                    })
                    ->where(function ($lmq) {
                        $lmq->whereNull('guide_id')->orWhere('guide_id', 0);
                    });
            }])->offset($start * $limit)->limit($limit)->get(['id', 'total_product_price'])->toArray();
            //没有订单退出循环
            if (empty($orderList)) {
                break;
            } else {
                $start++;
            }
            //筛选出没有分类数据的sku
            $requestSku = [];
            foreach ($orderList as $orderKey => $order) {
                if (empty($order['order_data_item'])) {
                    unset($orderList[$orderKey]);
                    continue;
                }
                foreach ($order['order_data_item'] as $item) {
                    if (!isset($skuData[$item['sku']]) && !in_array($item['sku'], $requestSku)) {
                        $requestSku[] = $item['sku'];
                    }
                }
            }
            //获取sku所属的顶级分类
            if (!empty($requestSku)) {
                $skuInfoBack = $Http->curl('outward/product/getProductInfoBySkuIds', ['sku_ids' => implode(',', $requestSku)]);
                foreach ($requestSku as $skuId) {
                    $prodInfo = isset($skuInfoBack['data'][$skuId]) && !empty($skuInfoBack['data'][$skuId]) ? $skuInfoBack['data'][$skuId] : [];
                    $cateTree = isset($skuInfoBack['data'][$skuId]) && !empty($skuInfoBack['data'][$skuId]['priority_cat_tree']) ? $skuInfoBack['data'][$skuId]['priority_cat_tree'] : [];
                    $skuData[$skuId]['prodId'] = $prodInfo ? $prodInfo['product_id'] : '';
                    $skuData[$skuId]['name'] = $prodInfo ? $prodInfo['product_name'] : '';
                    $skuData[$skuId]['kvImage'] = $prodInfo ? $prodInfo['kv_image'] : '';
                    $skuData[$skuId]['catName'] = $cateTree ? $cateTree[0]['cat_name'] : '';
                    $skuData[$skuId]['treeIds'] = $cateTree ? array_column($cateTree, 'id') : [];
                }
            }
            //计算每个分类下的sku销量
            foreach ($orderList as $order) {
                foreach ($order['order_data_item'] as $item) {
                    $prodSales = self::constructSalesData($prodSales, $skuData, $item, $lastDate['date']);
                    if (isset($transCate[$skuData[$item['sku']]['catName']])) {
                        $cateSalesAmount[$transCate[$skuData[$item['sku']]['catName']]]['amount'] += $item['product_amount_total'] * $item['qty'];
                    } else {
                        $cateSalesAmount['other']['amount'] += $item['product_amount_total'] * $item['qty'];
                    }
                    foreach ($cateSpecSalesAmount as $cateKey => $cateSpecSale) {
                        if (in_array($cateSpecSale['id'], $skuData[$item['sku']]['treeIds'])) {
                            $cateSpecSalesAmount[$cateKey]['details'] = self::constructSalesData($cateSpecSale['details'], $skuData, $item, $lastDate['date'], 'online', $cateSpecSale['type']);
                        }
                    }
                }
            }
        }

        return ['cateSalesAmount' => $cateSalesAmount, 'cateSpecSalesAmount' => $cateSpecSalesAmount, 'prodSales' => $prodSales];
    }

    /**
     * O2O销售-各分类销量-每日
     * @return mixed
     */
    public static function deviseO2OSalesCountCountDaily()
    {
        $cateSpecSalesAmount = [
            [
                'id' => 13,
                'name' => '明星系列',
                'type' => 'star',
                'details' => []
            ],
        ];
        $lastDate = self::lastDayDate();

        $start = 0;
        $limit = 100;

        $Http = new Http();

        $skuData = [];
        $prodSales = [];
        while (true) {
            //获取当日订单的商品数据
            $orderList = Order::orWhere(function ($query) use ($lastDate)  {
                $query->where('payment_type', '<>', 5)->where('order_type', '<>' ,2)->where('transaction_date', $lastDate['date']);
            })->orwhere(function($query1) use ($lastDate){
                $query1->where('payment_type', 5)->whereDate('send_at', $lastDate['date'])->where('order_type', '<>' ,2);
            })->with(['orderDataItem' => function ($query) {
                $query->select('order_main_id', 'sku', 'qty', 'product_amount_total')
                    ->where('is_gift', 0)
                    ->where('is_free', 0)
                    ->where(function ($mq) {
                        $mq->where('type', 1)->orWhere('type', 3)->orWhere(function ($fq) {
                            $fq->where('type', 2)->where('collections', '');
                        });
                    })
                    ->whereNotNull('guide_id')
                    ->where('guide_id', '<>', 0);
            }])->offset($start * $limit)->limit($limit)->get(['id', 'total_product_price'])->toArray();
            //没有订单退出循环
            if (empty($orderList)) {
                break;
            } else {
                $start++;
            }
            //筛选出没有分类数据的sku
            $requestSku = [];
            foreach ($orderList as $orderKey => $order) {
                if (empty($order['order_data_item'])) {
                    unset($orderList[$orderKey]);
                    continue;
                }
                foreach ($order['order_data_item'] as $item) {
                    if (!isset($skuData[$item['sku']]) && !in_array($item['sku'], $requestSku)) {
                        $requestSku[] = $item['sku'];
                    }
                }
            }
            //获取sku所属的顶级分类
            if (!empty($requestSku)) {
                $skuInfoBack = $Http->curl('outward/product/getProductInfoBySkuIds', ['sku_ids' => implode(',', $requestSku)]);
                foreach ($requestSku as $skuId) {
                    $prodInfo = isset($skuInfoBack['data'][$skuId]) && !empty($skuInfoBack['data'][$skuId]) ? $skuInfoBack['data'][$skuId] : [];
                    $cateTree = isset($skuInfoBack['data'][$skuId]) && !empty($skuInfoBack['data'][$skuId]['priority_cat_tree']) ? $skuInfoBack['data'][$skuId]['priority_cat_tree'] : [];
                    $skuData[$skuId]['prodId'] = $prodInfo ? $prodInfo['product_id'] : '';
                    $skuData[$skuId]['name'] = $prodInfo ? $prodInfo['product_name'] : '';
                    $skuData[$skuId]['kvImage'] = $prodInfo ? $prodInfo['kv_image'] : '';
                    $skuData[$skuId]['catName'] = $cateTree ? $cateTree[0]['cat_name'] : '';
                    $skuData[$skuId]['treeIds'] = $cateTree ? array_column($cateTree, 'id') : [];
                }
            }
            //计算每个分类下的sku销量
            foreach ($orderList as $order) {
                foreach ($order['order_data_item'] as $item) {
                    foreach ($cateSpecSalesAmount as $cateKey => $cateSpecSale) {
                        if (in_array($cateSpecSale['id'], $skuData[$item['sku']]['treeIds'])) {
                            $cateSpecSalesAmount[$cateKey]['details'] = self::constructSalesData($cateSpecSale['details'], $skuData, $item, $lastDate['date'], 'o2o', $cateSpecSale['type']);
                        }
                    }
                }
            }
        }

        return ['cateSpecSalesAmount' => $cateSpecSalesAmount];
    }

    private static function constructSalesData($prodSales, $skuData, $item, $lastDate, $scene = 'online', $type = 'normal')
    {
        if (isset($prodSales[$skuData[$item['sku']]['prodId']])) {
            $prodSales[$skuData[$item['sku']]['prodId']]['qty'] += $item['qty'];
            $prodSales[$skuData[$item['sku']]['prodId']]['amount'] += $item['product_amount_total'] * $item['qty'];
        } else {
            $prodSales[$skuData[$item['sku']]['prodId']] = [
                'product_id' => $skuData[$item['sku']]['prodId'],
                'product_name' => $skuData[$item['sku']]['name'],
                'kv_image' => $skuData[$item['sku']]['kvImage'],
                'qty' => $item['qty'],
                'type' => $type,
                'scene' => $scene,
                'amount' => $item['product_amount_total'] * $item['qty'],
                'date' => $lastDate,
            ];
        }
        return $prodSales;
    }

    /**
     * 新老客访问订单占比率
     * @param string $from
     * @return string
     * @throws \Exception
     */
    public static function orderAccountDaily($from = 'yesterday')
    {
        switch ($from) {
            case 'yesterday':
                $sRange = '-1 day';
                break;
            case 'week':
                $sRange = '-7 day';
                break;
            case 'month':
                $sRange = '-30 day';
                break;
            default:
                throw new \Exception('Out from range.');
        }
        //起始时间
        $fromDate = self::lastDayDate($sRange);
        //结束时间
        $endDate = self::lastDayDate();
        //统计订单
        $newUserSales = 0;
        $oldUserSales = 0;
        $rangeOrderList = Order::orWhere(function ($query) use ($fromDate,$endDate)  {
            $query->where('payment_type', '<>', 5)->where('order_type', '<>' ,2)->where('transaction_date', '>=', $fromDate['date'])->where('transaction_date', '<=', $endDate['date']);
        })->orwhere(function($query1) use ($fromDate,$endDate){
            $query1->where('payment_type', 5)->whereDate('send_at','>=', $fromDate['date'])->whereDate('send_at', '<=', $endDate['date'])->where('order_type', '<>' ,2);
        })->get(['id', 'user_id', 'total_product_price'])->toArray();


        //$rangeOrderList = Order::where('transaction_date', '>=', $fromDate['date'])->where('transaction_date', '<=', $endDate['date'])->where('order_type', '<>', 2)->get(['id', 'user_id', 'total_product_price'])->toArray();
        if (!empty($rangeOrderList)) {
            //统计老客
            //$oldUserList = Order::where('transaction_date', '<', $fromDate['date'])->where('channel', '<>', 0)->where('order_type', '<>', 2)->distinct('user_id')->get(['user_id'])->toArray();
            $oldUserList = Order::orWhere(function ($query) use ($fromDate,$endDate)  {
                $query->where('payment_type', '<>', 5)->where('order_type', '<>' ,2)->where('transaction_date', '<', $fromDate['date'])->where('channel', '<>', 0);
            })->orwhere(function($query1) use ($fromDate,$endDate){
                $query1->where('payment_type', 5)->whereDate('send_at','>=', $fromDate['date'])->where('order_type', '<>' ,2)->where('channel', '<>', 0);
            })->get(['user_id'])->toArray();
            $oldUserData = array_flip(array_column($oldUserList, 'user_id')) ?: [];
            $newUserSales = 0;
            $oldUserSales = 0;
            foreach ($rangeOrderList as $orderInfo) {
                if (isset($oldUserData[$orderInfo['user_id']])) {
                    //统计老客销量
                    $oldUserSales += $orderInfo['total_product_price'];
                } else {
                    //统计新客销量
                    $newUserSales += $orderInfo['total_product_price'];
                }
            }
            return $newUserSales . '|' .$oldUserSales;
        } else {
            return $newUserSales . '|' .$oldUserSales;
            return '无';
        }
    }

    /**
     * 老客的复购率
     * @param string $from
     * @return string
     * @throws \Exception
     */
    public static function userSecPurchaseDaily($from = 'yesterday')
    {
        switch ($from) {
            case 'yesterday':
                $sRange = '-1 day';
                break;
            case 'week':
                $sRange = '-7 day';
                break;
            case 'month':
                $sRange = '-30 day';
                break;
            default:
                throw new \Exception('Out from range.');
        }
        //起始时间
        $fromDate = self::lastDayDate($sRange);
        //结束时间
        $endDate = self::lastDayDate();
        //起始时间
        $yearDate = self::lastDayDate('-365 day');
        //统计这段时间内下单的人数

        $rangeUserCount = Order::orWhere(function ($query) use ($fromDate,$endDate)  {
            $query->where('payment_type', '<>', 5)->where('order_type', '<>' ,2)->where('transaction_date', '>=', $fromDate['date'])->where('transaction_date', '<=', $endDate['date']);
        })->orwhere(function($query1) use ($fromDate,$endDate) {
            $query1->where('payment_type', 5)->whereDate('send_at', '>=', $fromDate['date'])->whereDate('send_at', '<=', $endDate['date'])->where('order_type', '<>', 2);
        })->distinct('user_id')->count('user_id');
           //统计一年内下单的人数
        $yearUserCount = Order::orWhere(function ($query) use ($yearDate,$endDate)  {
            $query->where('payment_type', '<>', 5)->where('order_type', '<>' ,2)->where('transaction_date', '>=', $yearDate['date'])->where('transaction_date', '<=', $endDate['date']);
        })->orwhere(function($query1) use ($yearDate,$endDate){
            $query1->where('payment_type', 5)->whereDate('send_at','>=', $yearDate['date'])->whereDate('send_at', '<=', $endDate['date'])->where('order_type', '<>' ,2);
        })->distinct('user_id')->count('user_id');

        if($rangeUserCount<=0){
            return  '0%';
        }
        return round(($rangeUserCount / $yearUserCount) * 100, 3) . '%';
    }
}
