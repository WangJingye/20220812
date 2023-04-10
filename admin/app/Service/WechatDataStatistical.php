<?php
/**
 *  ===========================================
 *  File Name   WechatDataStatistical.php
 *  Date:       2019-10-24 13:34
 *  Created by  Inctone
 *  Use for: 微信小程序官方数据统计API
 *  Required:   PHP version ^5.5.0;
 *              allow_url_fopen参数必须打开;
 *              要使用cURL，cURL版本必须 >= 7.19.4,
 *              并且配置文件编译了OpenSSL 与 zlib。
 *  ===========================================
 **/

namespace App\Service;


use App\Service\Interfaces\WechatApi;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class WechatDataStatistical implements WechatApi
{
    public static $obj = null;
    
    //post-data
    private $params;
    
    //wechat api post action
    private $apiAction;
    
    //wechat api post uri
    private $apiUri;
    
    //the visitor access_token
    private $accessToken;
    
    // set the last search date
    private $searchLastDate;
    
    //set max search days
    private $searchDays = 0;
    
    /**
     * Disable external instantiation.
     *
     * @param $config
     */
    private function __construct($config)
    {
        $this->apiUri    = $config['api_uri'];
        $this->apiAction = $config['api_action'];
    }
    
    /**
     * Open a static interface to invoke non-static methods of the class
     *
     * @version
     * @return \App\Service\WechatDataStatistical|null
     * @author  inctone(2019-10-24)
     * @throws \Exception
     */
    public static function getConfig()
    {
        if (!self::$obj || !self::$obj instanceof WechatDataStatistical) {
            $config    = config('project.small_wechat_date_statistics.wechat_api');
            self::$obj = new self($config);
        }
        
        return self::$obj;
    }
    
    /**
     * 设置AccessToken
     *
     * @param string $accessToken
     *
     * @version
     * @return $this
     * @author  inctone(2019-10-24)
     */
    public function whereAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
        
        return $this;
        
    }
    
    /**
     * 设置最大查询时间
     *
     * @param string $maxDate
     * @param int    $searchDays
     *
     * @version
     * @return $this
     * @author  inctone(2019-10-25)
     * @throws \Exception
     */
    public function whereMaxDate(string $maxDate = '', $searchDays = 1)
    {
        if (!strtotime($maxDate) || date('Y-m-d', strtotime($maxDate)) != $maxDate) {
            throw new \Exception('查询时间格式错误', 1);
        }
        $this->searchLastDate = $maxDate && $maxDate < date('Y-m-d') ? $maxDate : date('Y-m-d', strtotime('-1 days'));
        
        if (!in_array($searchDays, [1, 7, 30])) {
            throw new \Exception("只能查询最近1/7/30天数据", 1);
        }
        
        $this->searchDays = $searchDays - 1;
        
        return $this;
    }
    
    /**
     * 获取用户访问小程序日留存
     *
     * @version
     * @return array|mixed
     * @author  inctone(2019-10-24)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDailyRetain()
    {
        // TODO: Implement getDailyRetain() method.
        //设置查询日期
        $this->vifLastDate(__FUNCTION__);
        $beginDate = $endDate = date('Ymd', strtotime($this->searchLastDate));
        $this->whereDate($beginDate, $endDate);
        
        //当前接口post数据中需要添加AccessToken
        $this->params['access_token'] = $this->accessToken;
        
        return $this->selected('daily_retain');
        //{"ref_date":"20191024","visit_uv_new":[{"key":0,"value":10272}],"visit_uv":[{"key":0,"value":25487}]}
    }
    
    public function getWeeklyRetain()
    {
        // TODO: Implement getWeeklyRetain() method.
        //设置查询日期
        $this->vifLastDate(__FUNCTION__);
        $beginDate = date('Ymd', strtotime('Sunday -6 day', strtotime($this->searchLastDate)));
        $endDate   = date('Ymd', strtotime('Sunday', strtotime($this->searchLastDate)));
        $this->whereDate($beginDate, $endDate);
        
        return $this->selected('weekly_retain');
    }
    
    /**
     * 获取用户访问小程序月留存
     *
     * @version
     * @return array|mixed
     * @author  inctone(2019-10-24)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMonthlyRetain()
    {
        // TODO: Implement getMonthlyRetain() method.
        //设置查询日期
        $this->vifLastDate(__FUNCTION__);
        $beginDate = date('Y-m-d', strtotime(date('Y-m-01') . ' -1 month'));
        $endDate   = date('Y-m-d', strtotime(date('Y-m-01') . ' -1 day'));
        if ($endDate > date('Ymd', strtotime('-1 day'))) {
            $endDate = date('Ymd', strtotime('-1 day'));
        }
        $this->whereDate($beginDate, $endDate);

        return $this->selected('monthly_retain');
        //"{"ref_date":"20191015","visit_uv_new":[{"key":0,"value":109141},{"key":1,"value":1187},{"key":2,"value":837},{"key":3,"value":770},{"key":4,"value":464},{"key":5,"value":616},{"key":6,"value":2618},{"key":7,"value":1663}],"visit_uv":[{"key":0,"value":119450},{"key":1,"value":2170},{"key":2,"value":1781},{"key":3,"value":1626},{"key":4,"value":1012},{"key":5,"value":1473},{"key":6,"value":3584},{"key":7,"value":2655}]}"
        
    }
    
    //获取用户访问小程序周留存
    
    /**
     * 获取用户访问小程序数据概况
     *
     * @version
     * @return array|mixed
     * @author  inctone(2019-10-24)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDailySummary()
    {
        // TODO: Implement getDailySummary() method.
        
        //设置查询日期
        $this->vifLastDate(__FUNCTION__);
        $beginDate = $endDate = date('Ymd', strtotime($this->searchLastDate));
        $this->whereDate($beginDate, $endDate);
        
        return $this->selected('daily_summary');
    }
    
    /**
     * 获取用户日访问小程序数据概况
     *
     * @version
     * @return array|mixed
     * @author  inctone(2019-10-24)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDailyVisitTrend()
    {
        // TODO: Implement getDailyVisitTrend() method.
        
        $this->vifLastDate(__FUNCTION__);
        $beginDate = $endDate = date('Ymd', strtotime($this->searchLastDate));
        $this->whereDate($beginDate, $endDate);
        
        return $this->selected('daily_visit_trend');
    }
    
    /**
     * 获取用户月访问小程序数据概况
     *
     * @version
     * @return array|mixed
     * @author  inctone(2019-10-25)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMonthlyVisitTrend()
    {
        // TODO: Implement getMonthlyVisitTrend() method.
        
        //设置查询日期
        $this->vifLastDate(__FUNCTION__);
        $beginDate = date('Ym01', strtotime($this->searchLastDate));
        $endDate   = date('Ymd', strtotime($beginDate.' +1 month -1 day'));
        if ($endDate > date('Ymd', strtotime('-1 day'))) {
            $endDate = date('Ymd', strtotime('-1 day'));
        }
        $this->whereDate($beginDate, $endDate);
        
        return $this->selected('monthly_visit_trend');
    }
    
    public function getWeeklyVisitTrend()
    {
        // TODO: Implement getWeeklyVisitTrend() method.
        
        //设置查询日期
        $this->vifLastDate(__FUNCTION__);
        $beginDate = date('Ymd', strtotime('Sunday -6 day', strtotime($this->searchLastDate)));
        $endDate   = date('Ymd', strtotime('Sunday', strtotime($this->searchLastDate)));
        $this->whereDate($beginDate, $endDate);
        
        return $this->selected('weekly_visit_trend');
    }
    
    /**
     * 获取小程序新增或活跃用户的画像分布数据
     *
     * @version
     * @return array|mixed
     * @author  inctone(2019-10-24)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserPortrait()
    {
        // TODO: Implement getUserPortrait() method.
        
        //设置查询日期
        $this->vifLastDate(__FUNCTION__);
        $endDate   = date('Ymd', strtotime($this->searchLastDate));
        $beginDate = date('Ymd', strtotime($this->searchLastDate.' -'.$this->searchDays.' day'));
        $this->whereDate($beginDate, $endDate);
        
        return $this->selected('user_portrait');
    }
    
    /**
     * 获取用户小程序访问分布数据
     *
     * @version
     * @return array|mixed
     * @author  inctone(2019-10-24)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getVisitDistribution()
    {
        // TODO: Implement getVisitDistribution() method.
        
        $this->vifLastDate(__FUNCTION__);
        $beginDate = $endDate = date('Ymd', strtotime($this->searchLastDate));
        $this->whereDate($beginDate, $endDate);
        
        return $this->selected('visit_distribution');
    }
    
    /**
     * 访问页面
     *
     * @version
     * @return array|mixed
     * @author  inctone(2019-10-28)
     * @throws \Exception
     */
    public function getVisitPage()
    {
        // TODO: Implement getVisitPage() method.
        
        $this->vifLastDate(__FUNCTION__);
        $beginDate = $endDate = date('Ymd', strtotime($this->searchLastDate));
        $this->whereDate($beginDate, $endDate);
        
        return $this->selected('visit_page');
    }
    
    /**
     * 设置查询时间范围
     *
     * @param string $begin_date
     * @param string $end_date
     *
     * @version
     * @return $this
     * @author  inctone(2019-10-24)
     */
    private function whereDate(string $begin_date, string $end_date)
    {
        $this->params['begin_date'] = $begin_date;
        $this->params['end_date']   = $end_date;
        
        return $this;
    }
    
    /**
     * 提交前验证数据
     *
     * @param string $method
     *
     * @version
     * @author  inctone(2019-10-28)
     * @throws \Exception
     */
    private function vifLastDate($method = '')
    {
        if (!$this->searchLastDate) {
            throw new \Exception('参数错误！未选择时间', 2003);
        }
        $lastDate = date('Ymd', strtotime($this->searchLastDate));
        switch ($method) {
            case 'getDailyRetain':
                //日留存
            case 'getDailySummary':
                //概况
            case 'getDailyVisitTrend':
                //日趋势
            case 'getVisitDistribution':
            case 'getVisitPage':
                //用户小程序访问分布数据
                
                $maxDate = date('Ymd', strtotime('-1 day'));
                if ($maxDate < $lastDate) {
                    throw new \Exception("查询日期最大不能超过昨日（max-date:{$maxDate}）", 2003);
                }
                break;
            case 'getWeeklyRetain':
                //周留存
            case 'getWeeklyVisitTrend':
                //周访问趋势
                
                $maxDate = date('Ymd', strtotime('Sunday -7 day'));
                if ($maxDate < $lastDate) {
                    throw new \Exception("查询周期最大不能超过上周（max-date:{$maxDate}）", 2003);
                }
                break;
            case 'getMonthlyRetain':
                //月留存
            case 'getMonthlyVisitTrend':
                //月访问趋势
                
                $maxDate = date('Ymd', strtotime(date('Ym01').' -1 day'));
                if ($maxDate < $lastDate) {
                    throw new \Exception("查询月份最大不能超过上月（max-date:{$maxDate}）", 2003);
                }
                break;
            case 'getUserPortrait':
                //用户画像
                
                $maxDate = date('Ymd', strtotime('-1 day'));
                if ($maxDate < $lastDate) {
                    throw new \Exception("查询日期最大不能超过昨日（max-date:{$maxDate}）", 2003);
                }
                
                if (!in_array($this->searchDays, [0, 6, 29])) {
                    throw new \Exception("只能查询最近1/7/30天数据", 2003);
                }
                break;
                default:
                
                throw new \Exception('非法访问！未定义的统计类型', 2100);
                
                break;
        }
    }
    
    /**
     * 发送请求,获取查询数据
     *
     * @param        $sendAction
     * @param string $sendType
     * @param string $apptType
     *
     * @version
     * @return mixed
     * @author  inctone(2019-10-28)
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function selected($sendAction, $sendType = 'post', $apptType = 'json')
    {
        //验证数据
        $this->dataValidator();
        
        //获取提交地址
        $sendUrl = $this->getPostUrl($sendAction);
        if (!$sendUrl) {
            throw new \Exception('参数错误，没有找到对应的api', 2002);
        }
        
        //组合参数，设置有效期为2秒
        $sendData = $this->getSendParams($sendType, $this->params, $apptType);
//        dump($sendUrl);
//        dump($sendData);
        //exit;
        //发送数据
        $client       = new Client();
        $response     = $client->request($sendType, $sendUrl, $sendData);
        $responseBody = json_decode($response->getBody()->getContents(), true);
        //dump($responseBody);exit;
        if (isset($responseBody['errcode']) && $responseBody['errcode'] !== 40001) {
            throw new \Exception($responseBody['errmsg'], $responseBody['errcode']);
        }

        return $responseBody;
    }
    
    /**
     * 获取提交地址
     *
     * @param string $apiAction
     *
     * @version
     * @return string
     * @author  inctone(2019-10-24)
     */
    private function getPostUrl(string $apiAction)
    {
        $apiAction = strtolower($apiAction);
        
        //获取实际的请求Action
        $sendAction = isset($this->apiAction[$apiAction]) ? $this->apiAction[$apiAction] : '';
        
        return $sendAction ? $this->apiUri.$sendAction.'?access_token='.$this->accessToken : '';
    }
    
    /**
     * 设置参数发送类型
     * 可增加其他header参数
     *
     * @param string $ptype  请求方式
     * @param array  $params 参数
     * @param string $dtype  请求类型
     *
     * @version
     * @return array
     * @author  inctone(2019-10-24)
     */
    private function getSendParams(string $ptype, array $params, string $dtype = 'json'):array
    {
        //设置超时时间
        $sendParams[RequestOptions::TIMEOUT] = 2;
        //设置交互方式
        if ($ptype == 'GET') {
            $sendParams[RequestOptions::QUERY] = $params;
        }
        else {
            switch ($dtype) {
                case "form":
                    $sendParams[RequestOptions::FORM_PARAMS] = $params;
                    break;
                case "json":
                default:
                    $sendParams[RequestOptions::JSON] = $params;
                    break;
            }
        }
        
        return $sendParams;
    }
    
    /**
     * 数据验证
     *
     * @version
     * @return $this
     * @author  inctone(2019-10-24)
     * @throws \Exception
     */
    private function dataValidator()
    {
        if (!isset($this->params['begin_date']) || !isset($this->params['end_date'])) {
            throw new \Exception('缺少时间查询条件', 2001);
        }
        
        $this->params['begin_date'] = $this->replaceStr($this->params['begin_date']);
        $this->params['end_date']   = $this->replaceStr($this->params['end_date']);
        
        return $this;
    }
    
    /**
     * 过滤符号
     *
     * @param $value
     *
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    private function replaceStr($value)
    {
        return strpos('-', $value) === false ? $value : str_replace(['-', ' ', ':'], '', $value);
    }
    
    /**
     * stop clone
     *
     * @version
     * @author  inctone(2019-10-24)
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
}
