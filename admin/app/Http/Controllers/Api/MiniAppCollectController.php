<?php
/**
 *  ===========================================
 *  File Name   Test.php
 *  Class Name  admin
 *  Date:       2019-10-24 15:33
 *  Created by
 *  Use for:
 *  ===========================================
 **/

namespace App\Http\Controllers\Api;


use App\Http\Helpers\Api\AdminCurlLog;
use App\Model\WxSmallDailySummary;
use App\Model\WxSmallRetainDaily;
use App\Model\WxSmallRetainMonthly;
use App\Model\WxSmallRetainWeekly;
use App\Model\WxSmallUserPortraitDaily;
use App\Model\WxSmallUserPortraitWeekly;
use App\Model\WxSmallUserPortraitMonthly;
use App\Model\WxSmallVisitDistribution;
use App\Model\WxSmallVisitPage;
use App\Model\WxSmallVisitTrendDaily;
use App\Model\WxSmallVisitTrendMonthly;
use App\Model\WxSmallVisitTrendWeekly;
use App\Service\WechatDataStatistical;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\MiniAppAccessToken;
use PHPMailer\PHPMailer\Exception;
use GuzzleHttp\Client;

class MiniAppCollectController extends Controller
{

    private $accessToken;
    private $retain_date;
    private $access_token_url;
    private $param;
    public function __construct()
    {
        $this->retain_date = date("Y-m-d", strtotime("-1 day"));

       $this->access_token_url = env('ACCESS_TOKEN_URL');// "https://wecadmin.chowsangsang.com.cn/api/miniapp/accessToken";
        $this->param['model'] = "admin";
        $this->param['force'] = 0;
        $response = http_Post_Data($this->access_token_url, $this->param);
        $this->accessToken = $response[1];
    }

    public function isTokenInvaild($data)
    {
        $tmpArr = json_decode($data, true);
        if(!isset($tmpArr['errcode'])){
            return false;
        }
        if ('40001' == $tmpArr['errcode']) {
            return true;
        }

        return false;
    }

    /**
     * 日留存，这个主要是查看最近一段时间的趋势
     *
     * @version
     * @return string
     * @author inctone(2019-10-28)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDataDailyRetain()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                                           ->whereAccessToken($this->accessToken)
                                           ->whereMaxDate($this->retain_date)
                                           ->getDailyRetain();
            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate($this->retain_date)
                    ->getDailyRetain();
            }
            //dump($result);exit;
            if (!isset($result['errcode'])) {

                $refDate = $result['ref_date'];

                $where = [
                    'ref_date' => $refDate,
                ];

                $data  = [
                    'visit_uv_new' => $result['visit_uv_new'],
                    'visit_uv'     => $result['visit_uv'],
                ];

                WxSmallRetainDaily::updateOrCreate($where, $data);

                return 'success';

            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开
            return $e->getMessage();
        }

        return 'fail';

    }

    /**
     * 月留存，这个主要记录按月的统计趋势
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDataMonthlyRetain()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                                           ->whereAccessToken($this->accessToken)
                                           ->whereMaxDate(date('Y-m-d', strtotime($this->retain_date . ' -1 month')))
                                           ->getMonthlyRetain();
            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate(date('Y-m-d', strtotime($this->retain_date . ' -1 month')))
                    ->getMonthlyRetain();
            }

            if (!isset($result['errcode'])) {
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理
                $refDate = isset($result['ref_date']) ? $result['ref_date'] : date('Ym', strtotime($this->retain_date . ' -1 month'));
                $refDate = $refDate . '01';

                $where = [
                    'ref_date' => $refDate,
                ];
                $data  = [
                    'visit_uv_new' => $result['visit_uv_new'],
                    'visit_uv'     => $result['visit_uv'],
                ];

                WxSmallRetainMonthly::updateOrCreate($where, $data);

                return 'success';
            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开
            return $e->getMessage();
        }

        return 'fail';

    }

    /**
     * 周留存，这个主要统计按周的统计趋势
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     */
    public function getDataWeeklyRetain()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                                           ->whereAccessToken($this->accessToken)
                                           ->whereMaxDate(date('Y-m-d', strtotime($this->retain_date . ' -1 week')))
                                           ->getWeeklyRetain();
            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate(date('Y-m-d', strtotime($this->retain_date . ' -1 week')))
                    ->getWeeklyRetain();
            }

            if (!isset($result['errcode'])) {
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理
                $refDate = explode('-', $result['ref_date']);

                $where = [
                    'ref_date_start' => $refDate[0],
                    'ref_date_end'   => $refDate[1],
                ];
                $data  = [
                    'visit_uv_new' => $result['visit_uv_new'],
                    'visit_uv'     => $result['visit_uv'],
                ];

                WxSmallRetainWeekly::updateOrCreate($where, $data);

                return 'success';

            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开

            return $e->getMessage();
        }

        return 'fail';

    }

    /**
     * 按天统计小程序访问pv，uv，分享等
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDataDailySummary()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                                           ->whereAccessToken($this->accessToken)
                                           ->whereMaxDate($this->retain_date)
                                           ->getDailySummary();
            //dump($result);
            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate($this->retain_date)
                    ->getDailySummary();
            }
            //dump($result);exit;
            if (!isset($result['errcode'])) {
                //取出数据
                $resultData = $result['list'][0];
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理

                $refDate = $resultData['ref_date'];

                //检查数据是否已存在，存在则更新不存在则添加

                $where = [
                    'ref_date' => $refDate,
                ];
                $data  = [
                    'visit_total' => $resultData['visit_total'],
                    'share_pv'    => $resultData['share_pv'],
                    'share_uv'    => $resultData['share_uv'],
                ];

                WxSmallDailySummary::updateOrCreate($where, $data);

                return 'success';
            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开
            return $e->getMessage();
        }

        return 'fail';

    }

    /**
     * 日趋势，按天统计小程序访问数据明细
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDataDailyVisitTrend()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                                           ->whereAccessToken($this->accessToken)
                                           ->whereMaxDate($this->retain_date)
                                           ->getDailyVisitTrend();
            //dump($result);
            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate($this->retain_date)
                    ->getDailyVisitTrend();
            }

            if (!isset($result['errcode'])) {
                //取出数据
                $resultData = $result['list'][0];
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理

                $refDate = $resultData['ref_date'];

                //检查数据是否已存在，存在则更新不存在则添加

                $where = [
                    'ref_date' => $refDate,
                ];
                $data  = [
                    'session_cnt'       => $resultData['session_cnt'],
                    'visit_pv'          => $resultData['visit_pv'],
                    'visit_uv'          => $resultData['visit_uv'],
                    'visit_uv_new'      => $resultData['visit_uv_new'],
                    'stay_time_uv'      => $resultData['stay_time_uv'],
                    'stay_time_session' => $resultData['stay_time_session'],
                    'visit_depth'       => $resultData['visit_depth'],
                ];

                WxSmallVisitTrendDaily::updateOrCreate($where, $data);

                return 'success';

            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开
            return $e->getMessage();
        }

        return 'fail';

    }

    /**
     * 周趋势，按周统计小程序访问的数据明细
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     */
    public function getDataWeeklyVisitTrend()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                                           ->whereAccessToken($this->accessToken)
                                           ->whereMaxDate($this->retain_date)
                                           ->getWeeklyVisitTrend();
            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate($this->retain_date)
                    ->getWeeklyVisitTrend();
            }

            if (!isset($result['errcode'])) {
                $resultData = $result['list'][0];
                //dump($resultData);
                //exit;
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理
                $refDate = explode('-', $resultData['ref_date']);
                $where   = [
                    'ref_date_start' => $refDate[0],
                    'ref_date_end'   => $refDate[1],
                ];

                $data = [
                    'session_cnt'       => $resultData['session_cnt'],
                    'visit_pv'          => $resultData['visit_pv'],
                    'visit_uv'          => $resultData['visit_uv'],
                    'visit_uv_new'      => $resultData['visit_uv_new'],
                    'stay_time_uv'      => $resultData['visit_pv'],
                    'stay_time_session' => $resultData['stay_time_session'],
                    'visit_depth'       => $resultData['visit_depth'],
                ];

                //dump($data);exit;

                WxSmallVisitTrendWeekly::updateOrCreate($where, $data);

                return 'success';

            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开

            return $e->getMessage();
        }

        return 'fail';

    }

    /**
     * 月趋势
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDataMonthlyVisitTrend()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                                           ->whereAccessToken($this->accessToken)
                                           ->whereMaxDate($this->retain_date)
                                           ->getMonthlyVisitTrend();
            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate($this->retain_date)
                    ->getMonthlyVisitTrend();
            }

            if (!isset($result['errcode'])) {
                $resultDate = $result['list'][0];
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理
                $refDate = $resultDate['ref_date'].'01';

                $where = [
                    'ref_date' => $refDate,
                ];
                $data  = [
                    'session_cnt'       => $resultDate['session_cnt'],
                    'visit_pv'          => $resultDate['visit_pv'],
                    'visit_uv'          => $resultDate['visit_uv'],
                    'visit_uv_new'      => $resultDate['visit_uv_new'],
                    'stay_time_uv'      => $resultDate['stay_time_uv'],
                    'stay_time_session' => $resultDate['stay_time_session'],
                    'visit_depth'       => $resultDate['visit_depth'],
                ];

                WxSmallVisitTrendMonthly::updateOrCreate($where, $data);

                return 'success';

            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开
            return $e->getMessage();
        }

        return 'fail';

    }


    /**
     * 获取小程序新增或活跃用户的单日画像分布数据
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDataUserPortraitDaily()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                ->whereAccessToken($this->accessToken)
                ->whereMaxDate($this->retain_date, 1)
                ->getUserPortrait();

            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate($this->retain_date, 1)
                    ->getUserPortrait();
            }

            if (!isset($result['errcode'])) {
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理
                //$result  = null;
                $refDate = explode('-', $result['ref_date']);
                $where   = [
                    'ref_date_start' => $refDate[0],
                    'ref_date_end'   => $refDate[0],
                    'user_type'      => 1
                ];
                $newUser = [];
                $newUser = [
                    'v_province'  => $result['visit_uv_new']['province'],
                    'v_city'      => $result['visit_uv_new']['city'],
                    'v_genders'   => $result['visit_uv_new']['genders'],
                    'v_devices'   => $result['visit_uv_new']['devices'],
                    'v_ages'      => $result['visit_uv_new']['ages'],
                    'v_platforms' => $result['visit_uv_new']['platforms'],
                    'v_index'     => isset($result['visit_uv_new']['index']) ? $result['visit_uv_new']['index'] : 0,
                ];
                $user    = [
                    'v_province'  => $result['visit_uv']['province'],
                    'v_city'      => $result['visit_uv']['city'],
                    'v_genders'   => $result['visit_uv']['genders'],
                    'v_devices'   => $result['visit_uv']['devices'],
                    'v_ages'      => $result['visit_uv']['ages'],
                    'v_platforms' => $result['visit_uv']['platforms'],
                    'v_index'     => isset($result['visit_uv']['index']) ? $result['visit_uv']['index'] : 0,
                ];

                WxSmallUserPortraitDaily::updateOrCreate($where, $newUser);

                $where['user_type'] = 0;
                WxSmallUserPortraitDaily::updateOrCreate($where, $user);

                return 'success';

            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开
            return $e->getMessage();
        }

        return 'fail';

    }

    /**
     * 获取小程序新增或活跃用户的单周画像分布数据
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDataUserPortraitWeekly()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                ->whereAccessToken($this->accessToken)
                ->whereMaxDate($this->retain_date, 7)
                ->getUserPortrait();

            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate($this->retain_date, 7)
                    ->getUserPortrait();
            }

            if (!isset($result['errcode'])) {
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理
                //$result  = null;
                $refDate = explode('-', $result['ref_date']);
                $where   = [
                    'ref_date_start' => $refDate[0],
                    'ref_date_end'   => $refDate[1],
                    'user_type'      => 1
                ];
                $newUser = [];
                $newUser = [
                    'v_province'  => $result['visit_uv_new']['province'],
                    'v_city'      => $result['visit_uv_new']['city'],
                    'v_genders'   => $result['visit_uv_new']['genders'],
                    'v_devices'   => $result['visit_uv_new']['devices'],
                    'v_ages'      => $result['visit_uv_new']['ages'],
                    'v_platforms' => $result['visit_uv_new']['platforms'],
                    'v_index'     => isset($result['visit_uv_new']['index']) ? $result['visit_uv_new']['index'] : 0,
                ];
                $user    = [
                    'v_province'  => $result['visit_uv']['province'],
                    'v_city'      => $result['visit_uv']['city'],
                    'v_genders'   => $result['visit_uv']['genders'],
                    'v_devices'   => $result['visit_uv']['devices'],
                    'v_ages'      => $result['visit_uv']['ages'],
                    'v_platforms' => $result['visit_uv']['platforms'],
                    'v_index'     => isset($result['visit_uv']['index']) ? $result['visit_uv']['index'] : 0,
                ];
                WxSmallUserPortraitWeekly::updateOrCreate($where, $newUser);

                $where['user_type'] = 0;
                WxSmallUserPortraitWeekly::updateOrCreate($where, $user);

                return 'success';

            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开
            return $e->getMessage();
        }

        return 'fail';

    }



    /**
     * 获取小程序新增或活跃用户的单周画像分布数据
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDataUserPortraitMonthly()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                ->whereAccessToken($this->accessToken)
                ->whereMaxDate($this->retain_date, 30)
                ->getUserPortrait();
            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate($this->retain_date, 30)
                    ->getUserPortrait();
            }

            if (!isset($result['errcode'])) {
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理
                //$result  = null;
                $refDate = explode('-', $result['ref_date']);
                $where   = [
                    'ref_date_start' => $refDate[0],
                    'ref_date_end'   => $refDate[1],
                    'user_type'      => 1
                ];
                $newUser = [];
                $newUser = [
                    'v_province'  => $result['visit_uv_new']['province'],
                    'v_city'      => $result['visit_uv_new']['city'],
                    'v_genders'   => $result['visit_uv_new']['genders'],
                    'v_devices'   => $result['visit_uv_new']['devices'],
                    'v_ages'      => $result['visit_uv_new']['ages'],
                    'v_platforms' => $result['visit_uv_new']['platforms'],
                    'v_index'     => isset($result['visit_uv_new']['index']) ? $result['visit_uv_new']['index'] : 0,
                ];
                $user    = [
                    'v_province'  => $result['visit_uv']['province'],
                    'v_city'      => $result['visit_uv']['city'],
                    'v_genders'   => $result['visit_uv']['genders'],
                    'v_devices'   => $result['visit_uv']['devices'],
                    'v_ages'      => $result['visit_uv']['ages'],
                    'v_platforms' => $result['visit_uv']['platforms'],
                    'v_index'     => isset($result['visit_uv']['index']) ? $result['visit_uv']['index'] : 0,
                ];
                WxSmallUserPortraitMonthly::updateOrCreate($where, $newUser);

                $where['user_type'] = 0;
                WxSmallUserPortraitMonthly::updateOrCreate($where, $user);

                return 'success';

            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开
            return $e->getMessage();
        }

        return 'fail';

    }


    /**
     * 小程序访问分布数据
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDataVisitDistribution()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                                           ->whereAccessToken($this->accessToken)
                                           ->whereMaxDate($this->retain_date)
                                           ->getVisitDistribution();
            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate($this->retain_date)
                    ->getVisitDistribution();
            }

            if (!isset($result['errcode'])) {
                //取出数据
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理
                //检查数据是否已存在，存在则更新不存在则添加

                $where = [
                    'ref_date' => $result['ref_date'],
                ];

                $access_source_session_cnt = [];
                $access_staytime_info      = [];
                $access_depth_info         = [];
                foreach ($result['list'] AS $val) {
                    if ($val['index'] == 'access_source_session_cnt') {
                        $access_source_session_cnt = $val['item_list'];
                    }
                    elseif ($val['index'] == 'access_staytime_info') {
                        $access_staytime_info = $val['item_list'];
                    }
                    elseif ($val['index'] == 'access_depth_info') {
                        $access_depth_info = $val['item_list'];
                    }
                }
                $data = [
                    'access_source_session_cnt' => $access_source_session_cnt,
                    'access_staytime_info'      => $access_staytime_info,
                    'access_depth_info'         => $access_depth_info
                ];

                WxSmallVisitDistribution::updateOrCreate($where, $data);
                return 'success';

            }
        }catch (\GuzzleHttp\Exception\RequestException $e){
            //todo 记录错误日志，与数据日志分割开
            return $e->getMessage();
        }

        return 'fail';

    }

    /**
     * 访问页面。目前只提供按 page_visit_pv 排序的 top200。
     *
     * @version
     * @return string
     * @author  inctone(2019-10-28)
     */
    public function getDataVisitPage()
    {
        try{
            $result = WechatDataStatistical::getConfig()
                                           ->whereAccessToken($this->accessToken)
                                           ->whereMaxDate($this->retain_date)
                                           ->getVisitPage();
            if($this->isTokenInvaild(json_encode($result))){
                $param = $this->param;
                $param['force'] =1;
                $access_token = http_Post_Data($this->access_token_url,$param)[1];
                $result = WechatDataStatistical::getConfig()
                    ->whereAccessToken($access_token)
                    ->whereMaxDate($this->retain_date)
                    ->getVisitPage();
            }

            if (!isset($result['errcode'])) {
                //取出数据
                //todo 将请求结果做日志记录，记录格式：[请求时间]:[接口名称【接口用途】]:[结果集（JSON）]
                //todo coding ....
                //数据处理
                //检查数据是否已存在，存在则更新不存在则添加

                $where = [
                    'ref_date' => $result['ref_date'],
                ];

                foreach ($result['list'] AS $val) {

                    $where['page_path'] = $val['page_path'];

                    $data = [];
                    $data = [
                        'page_visit_pv'    => $val['page_visit_pv'],
                        'page_visit_uv'    => $val['page_visit_uv'],
                        'page_staytime_pv' => $val['page_staytime_pv'],
                        'entrypage_pv'     => $val['entrypage_pv'],
                        'exitpage_pv'      => $val['exitpage_pv'],
                        'page_share_pv'    => $val['page_share_pv'],
                        'page_share_uv'    => $val['page_share_uv'],
                    ];

                    WxSmallVisitPage::updateOrCreate($where, $data);

                }

                return 'success';

            }
        }catch (\Exception $e){
            //todo 记录错误日志，与数据日志分割开
            return $e->getMessage();
        }

        return 'fail';

    }
}
