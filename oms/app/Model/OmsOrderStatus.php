<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class OmsOrderStatus extends Model
{
    protected $table = 'oms_order_status';

    public function getStatusMap(){

        $status_json = Redis::get('oms_status');
        if($status_json){
            return json_decode($status_json,true);
        }
        $status_info = $this->get();
        $states_info = $status_info->pluck('state_name', 'state')->toarray();
        $status_info = $status_info->pluck('status_name', 'status')->toarray();
        $status_json = [$states_info,$status_info];
        Redis::set('oms_status',json_encode($status_json));
        return [$states_info,$status_info];
    }
    /**
     * 功能：获得状态信息
     * @param int $statusId
     * @return OmsOrderStatus
     */
    public static function mapStatus(int $statusId) : OmsOrderStatus
    {
        $self = new static();
        $dataRaw = $self->where('id', $statusId)->first();
        return $dataRaw;
    }
}