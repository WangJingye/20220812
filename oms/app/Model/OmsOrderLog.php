<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OmsOrderLog extends Model
{
    protected $table = 'oms_order_logistics_info';
//    protected $fillable = ['*'];
    protected $guarded = ['id'];
//    public $timestamps = true;

}