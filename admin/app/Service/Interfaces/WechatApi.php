<?php
/**
 *  ===========================================
 *  File Name   WechatApi.php
 *  Class Name  admin
 *  Date:       2019-10-24 14:01
 *  Created by
 *  Use for:
 *  ===========================================
 **/

namespace App\Service\Interfaces;


interface WechatApi
{
    /**
     * 获取用户访问小程序日留存
     *
     * @see     https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-retain/analysis.getDailyRetain.html
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    public function getDailyRetain();
    
    /**
     * 获取用户访问小程序月留存
     *
     * @see     https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-retain/analysis.getMonthlyRetain.html
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    public function getMonthlyRetain();
    
    /**
     * 获取用户访问小程序周留存
     *
     * @see     https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-retain/analysis.getWeeklyRetain.html
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    public function getWeeklyRetain();
    
    /**
     * 获取用户访问小程序数据概况
     *
     * @see     https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/analysis.getDailySummary.html
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    public function getDailySummary();
    
    /**
     * 获取用户访问小程序数据日趋势
     *
     * @seeh    ttps://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-trend/analysis.getDailyVisitTrend.html
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    public function getDailyVisitTrend();
    
    /**
     * 获取用户访问小程序数据月趋势
     *
     * @see     https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/visit-trend/analysis.getMonthlyVisitTrend.html
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    public function getMonthlyVisitTrend();
    
    /**
     * 获取用户访问小程序数据周趋势
     *
     * @see     POST https://api.weixin.qq.com/datacube/getweanalysisappidweeklyvisittrend?access_token=ACCESS_TOKEN
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    public function getWeeklyVisitTrend();
    
    /**
     * 获取小程序新增或活跃用户的画像分布数据
     *
     * @see     https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/analysis.getUserPortrait.html
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    public function getUserPortrait();
    
    /**
     * 获取用户小程序访问分布数据
     *
     * @see     https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/data-analysis/analysis.getVisitPage.html
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    public function getVisitDistribution();
    
    /**
     * 访问页面。目前只提供按 page_visit_pv 排序的 top200。
     *
     * @see     POST https://api.weixin.qq.com/datacube/getweanalysisappidvisitpage?access_token=ACCESS_TOKEN
     * @version
     * @return mixed
     * @author  inctone(2019-10-24)
     */
    public function getVisitPage();
}
