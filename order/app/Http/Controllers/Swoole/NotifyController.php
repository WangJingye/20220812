<?php namespace App\Http\Controllers\Swoole;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class NotifyController extends BaseController
{
    public function Send(Request $request){
        $fd = $request->get('fd');
        log_json('websocket',[
            'type'=>'received',
            'fd'=>$fd,
        ]);
        if($fd && is_numeric($fd)){
            /**@var \Swoole\WebSocket\Server $server */
            $server = app('swoole');
            $info   = $server->getClientInfo($fd);
            if($info && $info['websocket_status'] == WEBSOCKET_STATUS_FRAME){
                log_json('websocket',[
                    'type'=>'success',
                    'fd'=>$fd,
                ]);
                $server->push($fd, json_encode(['code'=>1,'message'=>'支付成功']));
            }
        }
    }
}
