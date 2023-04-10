<?php namespace App\Swoole;

use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use Illuminate\Support\Facades\Redis;

class WebSocketService implements WebSocketHandlerInterface
{
    public $redis;
    public function __construct(){
        $this->redis = Redis::connection('ws');
    }

    public function onOpen(Server $server, Request $request)
    {
        //验证token
        $token = request()->get('token');
        $this->log([
            'type'=>'open',
            'token'=>$token,
        ]);
        $uid = \App\Support\Token::getUidByToken($token);
        $this->log([
            'type'=>'open',
            'uid'=>$uid,
        ]);
        if(!$uid){
            $server->push($request->fd,'need login');
            $server->close($request->fd);
        }
        //心跳包设置无效 只能自己跳
//        $heartbeat_idle_time = 180000;
//        $heartbeat_check_interval = 30000;
//        if($heartbeat_idle_time>0){
//            swoole_timer_tick($heartbeat_check_interval, function ($timer_id) use(
//                $server,$request,&$heartbeat_idle_time,$heartbeat_check_interval){
//                $server->push($request->fd,json_encode(['code'=>2,'message'=>'等待支付']));
//                $heartbeat_idle_time -= $heartbeat_check_interval;
//            });
//        }
    }
    public function onMessage(Server $server, Frame $frame)
    {
        try{
            $this->log([
                'type'=>'onMessage',
                'data'=>$frame->data,
            ]);
            $data = json_decode($frame->data);
            //记录订单号和客户端
            $key = 'Fd';
            $this->redis->zadd($key,$frame->fd,$data->orderId);
        }catch (\Exception $e){
            $this->log([
                'RespondData'=>$frame,
                'ErrorMessage'=>$e->getMessage(),
            ]);
            $server->push($frame->fd,'error');
            $server->close($frame->fd);
        }
    }
    public function onClose(Server $server, $fd, $reactorId)
    {
        $this->log([
            'type'=>'onClose',
            'fd'=>$fd,
        ]);
        try{
            //删除客户端记录
            $key = 'Fd';
            if($this->redis->exists($key)){
                $this->redis->zremrangebyscore($key,$fd,$fd);
            }
        }catch (\Exception $e){
            $this->log([
                'Fd'=>$fd,
                'ErrorMessage'=>$e->getMessage(),
            ]);
        }
    }

    protected function log($data){
        log_json('websocket',$data);
    }

}
