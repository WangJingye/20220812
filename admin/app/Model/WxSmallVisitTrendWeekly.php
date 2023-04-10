<?php
/**
 *  ===========================================
 *  File Name   WxSmallRetainDaily.php
 *  Class Name  admin
 *  Date:       2019-10-25 16:32
 *  Created by
 *  Use for:
 *  ===========================================
 **/

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class WxSmallVisitTrendWeekly extends Model
{
    protected $table = "wx_small_visit_trend_weekly";
    
    //不允许更新的字段
    protected $guarded = [];
}
