<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/1/13
 * Time: 16:32
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class WxSmallUserPortraitMonthly extends Model
{
    protected $table = "wx_small_user_portrait_monthly";

    protected $casts = [
        'v_province'  => 'array',
        'v_city'      => 'array',
        'v_genders'   => 'array',
        'v_devices'   => 'array',
        'v_ages'      => 'array',
        'v_platforms' => 'array',
        'v_index'     => 'array',
    ];

    //不允许更新的字段
    protected $guarded = [];
}