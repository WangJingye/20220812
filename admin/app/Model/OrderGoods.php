<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    protected $connection='order' ;

    protected $table="oms_orders_items";

    protected $primaryKey = 'id';
    
    protected $guarded=[];


}
