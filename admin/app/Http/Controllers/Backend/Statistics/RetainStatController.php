<?php
/**
 *  ===========================================
 *  File Name   StatisticsController.php
 *  Class Name  admin
 *  Date:       2019-10-30 09:57
 *  Created by
 *  Use for:
 *  ===========================================
 **/

namespace App\Http\Controllers\Backend\Statistics;


use App\Http\Controllers\Backend\Controller;
use App\Model\WxSmallRetainDaily;
use App\Model\WxSmallRetainMonthly;
use App\Model\WxSmallRetainWeekly;
use Illuminate\Http\Request;

class RetainStatController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        //dump(1231);exit;
        return view('backend.statistics.retain.index');
    }
    
    public function getDailyRetainData(Request $request)
    {
        $searchDailyDate = $request->get('searchDailyDate', null);
        if (!$searchDailyDate) {
            if (!$searchDailyDate) {
                $searchDailyDate = date("Y-m-d", strtotime("-1 day"));
            }
            //return $this->responseJson(400, '参数错误');
        }
        
        $info = WxSmallRetainDaily::where('ref_date', $searchDailyDate)->first();
        if (!$info) {
            return $this->responseJson(400, $searchDailyDate .'暂无数据可以查询', [
                'data_visit_new' => array_fill(0, 10, 0),
                'data_visit'     => array_fill(0, 10, 0),
            ]);
        }
        
        //定义图表数据格式
        $data_standard = [
            ['key' => 0, 'value' => 0],
            ['key' => 1, 'value' => 0],
            ['key' => 2, 'value' => 0],
            ['key' => 3, 'value' => 0],
            ['key' => 4, 'value' => 0],
            ['key' => 5, 'value' => 0],
            ['key' => 6, 'value' => 0],
            ['key' => 7, 'value' => 0],
            ['key' => 14, 'value' => 0],
            ['key' => 30, 'value' => 0],
        ];
        
        $visit_new = array_column($info->visit_uv_new, 'value', 'key');
        $userNew   = array_map(function ($val) use ($visit_new)
        {
            return $val['value'] = isset($visit_new[$val['key']]) ? $visit_new[$val['key']] : 0;
        }, $data_standard);
        
        $visit = array_column($info->visit_uv, 'value', 'key');
        $user  = array_map(function ($val) use ($visit)
        {
            return isset($visit[$val['key']]) ? $visit[$val['key']] : 0;
        }, $data_standard);
        
        return $this->responseJson(200, $searchDailyDate . '数据查询成功', [
            'data_visit_new' => $userNew,
            'data_visit'     => $user,
        ]);
    }
    
    /**
     * 月留存
     *
     * @param \Illuminate\Http\Request $request
     *
     * @version
     * @return array
     * @author  inctone(2019-11-01)
     */
    public function getMonthlyRetainData(Request $request)
    {
        $searchMonthlyDate = $request->get('searchMonthlyDate', null);
        if (!$searchMonthlyDate) {
            $searchMonthlyDate = getLastMonth();
        }
        
        $dateArr           = explode('-', $searchMonthlyDate);
        $searchMonthlyDate = $dateArr[0].'-'.$dateArr[1].'-01';
        
        $info = WxSmallRetainMonthly::where('ref_date', $searchMonthlyDate)->first();
        //dump($info);
        //exit;
        if (!$info) {
            return $this->responseJson(400, $dateArr[0].'年'.$dateArr[1] . '月暂无数据可以查询', [
                'data_visit_new' => array_fill(0, 2, 0),
                'data_visit'     => array_fill(0, 2, 0),
            ]);
        }
        
        //定义图表数据格式
        $data_standard = [
            ['key' => 0, 'value' => 0],
            ['key' => 1, 'value' => 0],
        
        ];
        
        $visit_new = array_column($info->visit_uv_new, 'value', 'key');
        $userNew   = array_map(function ($val) use ($visit_new)
        {
            return $val['value'] = isset($visit_new[$val['key']]) ? $visit_new[$val['key']] : 0;
        }, $data_standard);
        
        $visit = array_column($info->visit_uv, 'value', 'key');
        $user  = array_map(function ($val) use ($visit)
        {
            return isset($visit[$val['key']]) ? $visit[$val['key']] : 0;
        }, $data_standard);
        
        return $this->responseJson(200,  $dateArr[0].'年'.$dateArr[1]  . '月数据查询成功', [
            'data_visit_new' => $userNew,
            'data_visit'     => $user,
        ]);
    }
    
    /**
     * 周留存
     *
     * @param \Illuminate\Http\Request $request
     *
     * @version
     * @return array
     * @author  inctone(2019-11-01)
     */
    public function getWeeklyRetainData(Request $request)
    {
        $searchWeeklyDate = $request->get('searchWeeklyDate', null);
        if (!$searchWeeklyDate) {
            $searchWeeklyDate =date("Y-m-d", strtotime("last week monday"));
            //return $this->responseJson(400, '参数错误');
        }
        //dump($searchDailyDate);exit;
        //获取该日期的周起始时间
        
        $beginDate = date('Y-m-d', strtotime('Sunday -6 day', strtotime($searchWeeklyDate)));
        $endDate   = date('Y-m-d', strtotime('Sunday', strtotime($searchWeeklyDate)));
        
        $info = WxSmallRetainWeekly::where('ref_date_start', $beginDate)->where('ref_date_end', $endDate)->first();
        //dump($info);exit;
        if (!$info) {
            return $this->responseJson(400, $beginDate . " - "  . $endDate . '暂无数据', [
                'data_visit_new' => array_fill(0, 10, 0),
                'data_visit'     => array_fill(0, 10, 0),
            ]);
        }
        
        //定义图表数据格式
        $data_standard = [
            ['key' => 0, 'value' => 0],
            ['key' => 1, 'value' => 0],
            ['key' => 2, 'value' => 0],
            ['key' => 3, 'value' => 0],
            ['key' => 4, 'value' => 0],
        ];
        
        $visit_new = array_column($info->visit_uv_new, 'value', 'key');
        $userNew   = array_map(function ($val) use ($visit_new)
        {
            return $val['value'] = isset($visit_new[$val['key']]) ? $visit_new[$val['key']] : 0;
        }, $data_standard);
        
        $visit = array_column($info->visit_uv, 'value', 'key');
        $user  = array_map(function ($val) use ($visit)
        {
            return isset($visit[$val['key']]) ? $visit[$val['key']] : 0;
        }, $data_standard);
        
        return $this->responseJson(200, $beginDate . " - "  . $endDate . '数据查询成功', [
            'data_visit_new' => $userNew,
            'data_visit'     => $user,
            'search_dates'   => $beginDate.'~'.$endDate
        ]);
    }

}
