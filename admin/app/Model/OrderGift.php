<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class OrderGift extends Model
{
    protected $connection='order' ;

    protected $table="order_gift";

    protected $primaryKey = 'id';
    
    protected $guarded=[];


}
