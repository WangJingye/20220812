<?php namespace App\Services\ShipfeeTry;

use App\Model\Order;
use App\Lib\GuzzleHttp;
use Illuminate\Support\Facades\Log;

//可以再下一单付邮订单
class Revert
{
    public static function revertNumber($order_sn){
        try{
            $resp = app('ApiRequestInner')->request('shipFeeTryRevert','POST',[
                'order_sn'=>$order_sn,
            ]);
            return $resp['code'];
        }catch (\Exception $e) {
            $err_msg = $e->getMessage();
            Log::error($err_msg);
            return $err_msg;
        }
    }
}