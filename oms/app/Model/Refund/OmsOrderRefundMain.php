<?php

namespace App\Model\Refund;

use Illuminate\Database\Eloquent\Model;

class OmsOrderRefundMain extends Model
{
    protected $table = 'oms_order_refund_main';
    protected $guarded = ['id'];

    const STATUS = [
        'returning','returned','refunding','refunded'
    ];

    public function items(){
        return $this->hasMany(OmsOrderRefundItems::class,'refund_main_id','id');
    }

    public static function list($status=''){
        $model = self::with('items');
        if($status){
            $model->where('status',$status);
        }
        $size = request()->get('size',10);
        $list = $model->get();
        dd($list->toArray());
        return $list;
    }
}