<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    CONST ENG_TRANSFER_CHN = [
        'G_BAR_SELL' => '金片',
        'G_JW_EXCH' => '足金饰品',
        'G_JW_SELL' => '足金饰品',
        'PT950_JW_EXCH' => '950铂金饰品',
        'PT950_JW_SELL' => '950铂金饰品',
        '006' => '生生金宝',

    ];
    protected $table = 'gold_price_consultation';

    public function getPriceAttribute($value)
    {
        return number_format($value / 100, 2);
    }
}
