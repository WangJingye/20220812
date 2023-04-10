<?php

namespace App\Model\Refund;

use Illuminate\Database\Eloquent\Model;

class OmsOrderItemsRemain extends Model
{
    protected $table = 'oms_order_items_remain';
    protected $guarded = ['id'];

    const UPDATED_AT = NULL;
    const CREATED_AT = NULL;
}