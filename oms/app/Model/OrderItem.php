<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    protected $table = 'oms_order_items';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $fillable = ['*'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $guarded = ['id'];

//    public function order()
//    {
//        return $this->belongsTo('App\Model\Order', 'order_man_id', 'id');
//    }

}
