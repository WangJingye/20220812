<?php

namespace App\Model\Goods;

use Illuminate\Support\Facades\DB;

class StockLog extends Model
{
    //指定表名
    protected $table = 'tb_stock_log';
    protected $guarded = [];
    protected $types = [
        1 => '入库',
        2 => '下单扣除',
        3 => '订单失效返还 ',
        4 => '残次品',
        5 => '预支库存',
        6 => '后台手动增加',
        7 => '后台手动减少',
    ];


    public function getTypeAttribute($type)
    {
            return $this->types[$type];
    }


}
