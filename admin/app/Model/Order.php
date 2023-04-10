<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $connection='order' ;

    protected $table="orders";

    protected $primaryKey = 'id';
    
    protected $guarded=[];

//    public function selectCollection()
//    {
//        return $this->hasOne('App\Model\Collection', 'page_id', 'id');
//
//    }
    public function goods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'id');

    }
    public function gift(){
        return $this->hasMany(OrderGift::class, 'order_id', 'id');
    }
    public function diff(){
        return $this->hasOne(Diff::class, 'order_sn', 'order_sn');
    }
    public function customer(){
        return $this->hasOne(Member::class, 'customer_id', 'customer_id');
    }
    public function diffItems(){
        return $this->hasMany(DiffItems::class, 'order_sn', 'order_sn');
    }
    public function orderStatusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id', 'id');

    }

}
