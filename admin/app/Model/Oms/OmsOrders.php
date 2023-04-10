<?php
namespace App\Model\Oms;

use Illuminate\Database\Eloquent\Model;
use App\Model\Oms\OmsOrderItems;


class OmsOrders extends Model
{
    protected $connection='oms' ;

    protected $table="oms_order_main";

    protected $primaryKey = 'id';

    protected $guarded=[];

    public function OrderItems()
    {
        return $this->hasMany(OmsOrderItems::class, 'order_main_id', 'id');
    }


}