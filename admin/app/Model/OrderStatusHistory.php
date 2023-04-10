<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $connection='order' ;

    protected $table="order_status_history";

    protected $primaryKey = 'id';
    
    protected $guarded=[];

//    public function selectCollection()
//    {
//        return $this->hasOne('App\Model\Collection', 'page_id', 'id');
//
//    }


}
