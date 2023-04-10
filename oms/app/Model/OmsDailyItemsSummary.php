<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OmsDailyItemsSummary extends Model
{

    protected $table = 'oms_daily_items_summary';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $guarded = ['id'];

    /**
     * 指示模型是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}
