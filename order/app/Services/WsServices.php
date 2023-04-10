<?php namespace App\Services;

use Illuminate\Support\Facades\Redis;

class WsServices
{
    /**
     * 通知ws关闭对应客户端的连接
     * @param $order_id
     * @return bool
     */
    public static function Notify($order_id){
        try{
            $redis = Redis::connection('ws');
            log_json('websocket',[
                'type'=>'notify',
                'orderId'=>$order_id,
            ]);
            //根据订单号获取WS客户端ID
            $key = 'Fd';
            $fd = $redis->zscore($key,$order_id);
            log_json('websocket',[
                'type'=>'getfd',
                'fd'=>$fd,
            ]);
            //如果存在WS客户端ID则发送消息
            if($fd){
                $host = env('WEBSOCKET_HOST');
                $url = "http://{$host}/notify/send";
                $curl = new \Curl\Curl();
                //以http方式向WS客户端推送消息
                call_user_func([$curl,'POST'],$url,['fd'=>$fd]);
                log_json('websocket',[
                    'type'=>'request',
                    'url'=>$url,
                ]);
            }return true;
        }catch (\Throwable $e){
            return false;
        }
    }



}
