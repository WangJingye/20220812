<?php
/**
 *  ===========================================
 *  File Name   SummaryStatController.php
 *  Class Name  admin
 *  Date:       2019-10-30 09:57
 *  Created by
 *  Use for:
 *  ===========================================
 **/

namespace App\Http\Controllers\Backend\Statistics;


use App\Http\Controllers\Backend\Controller;
use App\Model\WxSmallDailySummary;
use App\Model\WxSmallRetainDaily;
use App\Model\WxSmallRetainMonthly;
use App\Model\WxSmallRetainWeekly;
use Illuminate\Http\Request;

class SummaryStatController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $this->getSummaryData($request);
        
        return view('backend.statistics.summary.index');
    }
    
    public function getSummaryData(Request $request)
    {
        $paginate = $request->get('limit', 10);
        //$searchDailyDate = $request->get('searchDailyDate', null);
        //if (!$searchDailyDate) {
        //    return $this->responseJson(400, '参数错误', [
        //        'data_visit_new' => array_fill(0, 10, 0),
        //        'data_visit'     => array_fill(0, 10, 0),
        //    ]);
        //    //return $this->responseJson(400, '参数错误');
        //}
        //dump(date('Y-m-d'));
        $list = WxSmallDailySummary::where('ref_date', '<', date('Y-m-d'))
                                   ->orderBy('ref_date', 'desc')
                                   ->paginate($paginate)
                                   ->toArray();

        //if (!$list) {
        //    return $this->responseJson(400, '暂无数据', [
        //        'data_visit_new' => array_fill(0, 10, 0),
        //        'data_visit'     => array_fill(0, 10, 0),
        //    ]);
        //}
        
        ////定义图表数据格式
        //$data_standard = [
        //    ['key' => 0, 'value' => 0],
        //    ['key' => 1, 'value' => 0],
        //    ['key' => 2, 'value' => 0],
        //    ['key' => 3, 'value' => 0],
        //    ['key' => 4, 'value' => 0],
        //    ['key' => 5, 'value' => 0],
        //    ['key' => 6, 'value' => 0],
        //    ['key' => 7, 'value' => 0],
        //    ['key' => 14, 'value' => 0],
        //    ['key' => 30, 'value' => 0],
        //];
        //
        //$visit_new = array_column($info->visit_uv_new, 'value', 'key');
        //$userNew   = array_map(function ($val) use ($visit_new)
        //{
        //    return $val['value'] = isset($visit_new[$val['key']]) ? $visit_new[$val['key']] : 0;
        //}, $data_standard);
        //
        //$visit = array_column($info->visit_uv, 'value', 'key');
        //$user  = array_map(function ($val) use ($visit)
        //{
        //    return isset($visit[$val['key']]) ? $visit[$val['key']] : 0;
        //}, $data_standard);
        //$list['data']['total'] = $list['total'];
        return $this->responseJson(200, 'success', $list['data'],$list['total']);
    }
}
