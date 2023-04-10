<?php
/**
 *  ===========================================
 *  File Name   VisitTrendStatController.php
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
use App\Model\WxSmallVisitPage;
use App\Model\WxSmallVisitTrendDaily;
use App\Model\WxSmallVisitTrendMonthly;
use App\Model\WxSmallVisitTrendWeekly;
use Illuminate\Http\Request;

class VisitTrendStatController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return view('backend.statistics.visit_trend.index');
    }
    
    /**
     * 天趋势
     *
     * @param \Illuminate\Http\Request $request
     *
     * @version
     * @return array
     * @author  inctone(2019-11-01)
     */
    public function getDailyVisitTrendData(Request $request)
    {
        $searchDate = $request->get('searchDailyDate', null);
        if (!$searchDate) {
            $searchDate = date("Y-m-d", strtotime("-1 day"));
        }
        $info = WxSmallVisitTrendDaily::where('ref_date', $searchDate)->first();
        if (!$info) {
            return $this->responseJson(400, $searchDate . '暂无数据可以提供', [
                'ref_date'          => date("Y-m-d", strtotime("-1 day")),
                'stay_time_uv'      => 0,
                'stay_time_session' => 0,
                'visit_depth'       => 0,
                'info'              => array_fill(0, 6, 0),
            ]);
        }
        
        //定义图表数据格式
        $data_standard = [
            'session_cnt'  => 0,
            'visit_pv'     => 0,
            'visit_uv'     => 0,
            'visit_uv_new' => 0,
        ];
        
        $data['ref_date']          = isset($info['ref_date']) ? $info['ref_date'] : '';
        $data['stay_time_uv']      = isset($info['stay_time_uv']) ? $info['stay_time_uv'] : 0;
        $data['stay_time_session'] = isset($info['stay_time_session']) ? $info['stay_time_session'] : 0;
        $data['visit_depth']       = isset($info['visit_depth']) ? $info['visit_depth'] : 0;
        $data['visit_uv_new']       = isset($info['visit_uv_new']) ? $info['visit_uv_new'] : 0;
        $data['visit_uv']       = isset($info['visit_uv']) ? $info['visit_uv'] : 0;
        $data['share_pv']       = isset($info['share_pv']) ? $info['share_pv'] : 0;

        $data['share_uv']       = isset($info['share_uv']) ? $info['share_uv'] : 0;

        $listInfo = [];
        foreach ($data_standard AS $key => $val) {
            $listInfo[] = isset($info[$key]) ? $info[$key] : 0;
        }
        $data['info'] = $listInfo;
        
        return $this->responseJson(200, $searchDate . "数据查询成功", $data);
    }
    
    /**
     * 月趋势
     *
     * @param \Illuminate\Http\Request $request
     *
     * @version
     * @return array
     * @author  inctone(2019-11-01)
     */
    public function getMonthlyVisitTrendData(Request $request)
    {
        $searchMonthlyDate = $request->get('searchMonthlyDate', null);
        if (!$searchMonthlyDate) {
            $searchMonthlyDate = getLastMonth();
        }

        $dateArr           = explode('-', $searchMonthlyDate);
        $searchMonthlyDate = $dateArr[0].'-'.$dateArr[1].'-01';
        
        $info = WxSmallVisitTrendMonthly::where('ref_date', $searchMonthlyDate)->first();
        
        if (!$info) {
            return $this->responseJson(400,$dateArr[0].'年'.$dateArr[1]. '月暂无数据可以提供', [
                'ref_date'          => 0,
                'stay_time_uv'      => 0,
                'stay_time_session' => 0,
                'visit_depth'       => 0,
                'info'              => array_fill(0, 6, 0),
            ]);
        }
        
        //定义图表数据格式
        $data_standard             = [
            'session_cnt'  => 0,
            'visit_pv'     => 0,
            'visit_uv'     => 0,
            'visit_uv_new' => 0,
        ];
        $data['ref_date']          = isset($info['ref_date']) ? $info['ref_date'] : '';
        $data['stay_time_uv']      = isset($info['stay_time_uv']) ? $info['stay_time_uv'] : 0;
        $data['stay_time_session'] = isset($info['stay_time_session']) ? $info['stay_time_session'] : 0;
        $data['visit_depth']       = isset($info['visit_depth']) ? $info['visit_depth'] : 0;
        
        $listInfo = [];
        foreach ($data_standard AS $key => $val) {
            $listInfo[] = isset($info[$key]) ? $info[$key] : 0;
        }
        $data['info'] = $listInfo;
        
        return $this->responseJson(200,  $dateArr[0].'年'.$dateArr[1] ."月数据查询成功", $data);
    }
    
    /**
     * 周趋势
     *
     * @param \Illuminate\Http\Request $request
     *
     * @version
     * @return array
     * @author  inctone(2019-11-01)
     */
    public function getWeeklyVisitTrendData(Request $request)
    {
        $searchWeeklyDate = $request->get('searchWeeklyDate', null);
        if (!$searchWeeklyDate) {
            $searchWeeklyDate =date("Y-m-d", strtotime("last week monday"));
            //没给日期，默认用上周日期
            //return $this->responseJson(400, '参数错误');
        }
        //dump($searchDailyDate);exit;
        //获取该日期的周起始时间
        
        $beginDate = date('Y-m-d', strtotime('Sunday -6 day', strtotime($searchWeeklyDate)));
        $endDate   = date('Y-m-d', strtotime('Sunday', strtotime($searchWeeklyDate)));
        
        $info = WxSmallVisitTrendWeekly::where('ref_date_start', $beginDate)->where('ref_date_end', $endDate)->first();
        
        if (!$info) {
            return $this->responseJson(400, $beginDate . "-" . $endDate . '暂无数据可以提供', [
                'ref_date'          => 0,
                'stay_time_uv'      => 0,
                'stay_time_session' => 0,
                'visit_depth'       => 0,
                'info'              => array_fill(0, 6, 0),
            ]);
        }
        
        //定义图表数据格式
        $data_standard = [
            'session_cnt'  => 0,
            'visit_pv'     => 0,
            'visit_uv'     => 0,
            'visit_uv_new' => 0,
        ];
        
        $data['ref_date'] = isset($info['ref_date_start']) && isset($info['ref_date_end']) ?
            $info['ref_date_start'].'~'.$info['ref_date_end'] : '';
        
        $data['stay_time_uv']      = isset($info['stay_time_uv']) ? $info['stay_time_uv'] : 0;
        $data['stay_time_session'] = isset($info['stay_time_session']) ? $info['stay_time_session'] : 0;
        $data['visit_depth']       = isset($info['visit_depth']) ? $info['visit_depth'] : 0;
        
        $listInfo = [];
        foreach ($data_standard AS $key => $val) {
            $listInfo[] = isset($info[$key]) ? $info[$key] : 0;
        }
        $data['info'] = $listInfo;
        
        return $this->responseJson(200, $beginDate . "-" . $endDate  . "数据查询成功" , $data);
    }

}
