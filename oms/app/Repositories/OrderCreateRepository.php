<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/16
 * Time: 10:32
 */

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderCreateRepository
{
    private $order_detail_table = "oms_order_detail";

    public static $OrderStatus = [
        ['value'=>'WAIT_BUYER_PAY','name'=>'待付款','show'=>1],
        ['value'=>'WAIT_SELLER_SEND_GOODS','name'=>'待发货','show'=>1],
        ['value'=>'SELLER_CONSIGNED_PART','name'=>'部分发货','show'=>0],
        ['value'=>'WAIT_BUYER_CONFIRM_GOODS','name'=>'已发货','show'=>1],
        ['value'=>'TRADE_BUYER_SIGNED','name'=>'确认收货','show'=>0],
        ['value'=>'TRADE_FINISHED','name'=>'交易成功','show'=>0],
        ['value'=>'TRADE_CLOSED_BY_TAOBAO','name'=>'已取消','show'=>1],
        ['value'=>'REFUNDING','name'=>'退货中','show'=>1],
        ['value'=>'TRADE_CLOSED','name'=>'已退款','show'=>1],
    ];




}

