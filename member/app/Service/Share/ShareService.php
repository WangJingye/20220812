<?php namespace App\Service\Share;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Jobs\ShareQueued as Queued;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Model\Share\{ShareEvent,ShareNum};
use App\Exceptions\EventExpireException;
use Zend\Http\Request;

/**
 * @author Steven
 * Class ShareService
 * @package App\Service\Share
 */
class ShareService
{
    //当前活动信息
    const CURRENT_EVENT = 'share:current_event';
    //绑定关系缓存
    const IS_BIND = 'share:is_bind';
    //被分享着是否已经成功参与过
    const FRIEND_IS_PLAYED = 'share:friend_is_played';
    //邀请者邀请的好友
    const FRIEND = 'share:friend';

    protected $redis;
    protected $event;
    /**
     * ShareService constructor.
     * @throws \Exception
     */
    public function __construct(){
        $this->redis = Redis::connection();
        $this->initCurrentEventId();
    }

    /**
     * 获取第一个开启的作为当前的活动ID
     * @throws \Exception
     */
    public function initCurrentEventId(){
        $event = $this->redis->hgetall(self::CURRENT_EVENT);
        if(empty($event)){
            $event = ShareEvent::query()->where('status',1)->orderBy('id')->first();
            if($event){
                $event = $event->toArray();
                $this->redis->hmset(self::CURRENT_EVENT,$event);
            }else{
                throw new EventExpireException('活动不存在');
            }
        }
        $this->event = $event;
        //检查该活动是否在有效期内
        $date = date('Y-m-d H:i:s');
        if($date<$this->event['start_time'] || $date>$this->event['end_time']){
            throw new EventExpireException('活动已结束');
        }
    }

    /**
     * 建立绑定关系
     * @param $sharer_id
     * @param $friend_id
     * @return array
     */
    public function bindRelation($sharer_id,$friend_id){
        $result = [
            'shareType'=>$this->event['type'],
        ];
        if($sharer_id && $friend_id && $sharer_id != $friend_id){
            if(!$this->checkIsBind($sharer_id,$friend_id)) {
                $sql = "insert ignore share_relation (event_id,sharer_id,friend_id) values(?,?,?)";
                $res = DB::insert($sql, [$this->event['id'],$sharer_id,$friend_id]);
                if ($res) {
                    $this->makeBind($sharer_id, $friend_id);
                }
            }
        }
        return $result;
    }

    /**
     * 判断是否已经成功绑定过关系
     * @param $sharer_id
     * @param $friend_id
     * @return int
     */
    public function checkIsBind($sharer_id,$friend_id){
        $key = self::IS_BIND.":{$this->event['id']}";
        $value = self::getBindValue($sharer_id,$friend_id);
        return $this->redis->hexists($key,$value);
    }

    /**
     * 获取缓存中的绑定关系键值格式
     * @param $sharer_id
     * @param $friend_id
     * @return string
     */
    protected static function getBindValue($sharer_id,$friend_id){
        return "{$sharer_id}::{$friend_id}";
    }

    /**
     * 将绑定关系记录到缓存中
     * @param $sharer_id
     * @param $friend_id
     * @return int
     */
    private function makeBind($sharer_id,$friend_id){
        $key = self::IS_BIND.":{$this->event['id']}";
        $value = self::getBindValue($sharer_id,$friend_id);
        return $this->redis->hset($key,$value,time());
    }

    /**
     * 算人头加入队列
     * @param $params
     */
    public function notify($params){
        //1:拉新,2:下单
        $type = array_get($params,'type');
        if($type == ShareEvent::TYPE_REGISTER){
            $userId =  array_get($params,'userId');
            if($userId){
                self::registerNotifyDispatcher($userId);
            }
        }elseif($type == ShareEvent::TYPE_PAY){
            $userId =  array_get($params,'userId');
            $orderId =  array_get($params,'orderId');
            if($userId && $orderId){
                self::payNotifyDispatcher($userId,$orderId);
            }
        }
    }

    /**
     * 邀新成功后通知算人头加入队列
     * @param $userId
     */
    protected static function registerNotifyDispatcher($userId){
        if($userId){
            app(Dispatcher::class)->dispatch(new Queued(
                'registerNotify',
                compact('userId'))
            );
        }
    }

    /**
     * 支付成功后通知算人头加入队列
     * @param $userId
     * @param $orderId
     */
    protected static function payNotifyDispatcher($userId,$orderId){
        if($userId && $orderId){
            app(Dispatcher::class)->dispatch(new Queued(
                'payNotify',
                compact('userId','orderId'))
            );
        }
    }

    /**
     * 注册算人头
     * @param $userId
     * @return mixed|string
     */
    public function handleRegisterNotify($userId){
        if($this->event['type'] == ShareEvent::TYPE_REGISTER){
            return $this->handleNotify($userId);
        }return '未知的type';
    }

    /**
     * 支付下单算人头
     * @param $userId
     * @param $orderId
     * @return mixed|string
     */
    public function handlePayNotify($userId,$orderId){
        if($this->event['type'] == ShareEvent::TYPE_PAY){
            return $this->handleNotify($userId,['order_id'=>$orderId]);
        }return '未知的type';
    }

    /**
     * 执行算人头
     * @param $userId
     * @param $args
     * @return mixed|string
     */
    protected function handleNotify($userId,$args = []){
        try{
            if($this->checkIsPlayed($userId)){
                return '该用户已经参与过此活动';
            }
            return DB::transaction(function () use($userId,$args){
                //获取对应的关系
                $relation = DB::table('share_relation')->where('event_id',$this->event['id'])
                    ->where('friend_id',$userId)
                    ->orderBy('id')->first();
                if(empty($relation)){
                    throw new \Exception('找不到关联关系');
                }
                //标记关系为有效
                $order_id = array_get($args,'order_id')?:'';
                DB::table('share_relation')->where('id',$relation->id)->update(['status'=>1,'order_id'=>$order_id]);
                //分享人邀请数量+1
                $shareNum = ShareNum::query()
                    ->where('event_id',$this->event['id'])
                    ->where('sharer_id',$relation->sharer_id);
                if($shareNum->count()){
                    $shareNum->increment('num');
                    $num = $shareNum->value('num');
                }else{
                    ShareNum::query()->insert([
                        'event_id'=>$this->event['id'],
                        'sharer_id'=>$relation->sharer_id,
                    ]);
                    $num = 1;
                }
                $this->playedCache($relation->sharer_id,$relation->friend_id);
                return true;
            });
        }catch (\Exception $e){
            return $e->getMessage().',file:'.$e->getFile().',line:'.$e->getLine().',event_id:'.$this->event['id']??0;
        }
    }

    /**
     * 成功助力后缓存
     * @param $sharer_id
     * @param $friend_id
     */
    protected function playedCache($sharer_id,$friend_id){
        //为分享者添加好友
        $key = self::FRIEND.":{$this->event['id']}:{$sharer_id}";
        $this->redis->hset($key,$friend_id,time());
        //标记被分享着已参与过此次活动(成为有效好友)
        $key = self::FRIEND_IS_PLAYED.":{$this->event['id']}";
        $this->redis->hset($key,$friend_id,time());
    }

    protected function checkIsPlayed($friend_id){
        $key = self::FRIEND_IS_PLAYED.":{$this->event['id']}";
        return $this->redis->hexists($key,$friend_id);
    }

    ##初始化数据脚本###################################################################################
    public static function initDataCommand(){
        $redis = Redis::connection();
        $all_share_relation = DB::table('share_relation')->get();
        foreach($all_share_relation->chunk(5000) as $chunk){
            $bind_data = [];
            $friend_is_play_data = [];
            $friend_data = [];
            foreach($chunk as $item){
                $bind_data[$item->event_id][self::getBindValue($item->sharer_id,$item->friend_id)] = strtotime($item->created_at);
                if($item->status==1){
                    $friend_is_play_data[$item->event_id][$item->friend_id] = strtotime($item->created_at);
                    $friend_data[$item->event_id][$item->sharer_id][$item->friend_id] = strtotime($item->created_at);
                }
            }
            foreach($bind_data as $k=>$v){
                $key = self::IS_BIND.":{$k}";
                $redis->hmset($key,$v);
            }
            foreach($friend_is_play_data as $k=>$v){
                $key = self::FRIEND_IS_PLAYED.":{$k}";
                $redis->hmset($key,$v);
            }
            foreach($friend_data as $k=>$v){
                foreach($v as $kk=>$vv){
                    $key = self::FRIEND.":{$k}:{$kk}";
                    $redis->hmset($key,$vv);
                }
            }
        }

        return true;
    }
    ##额外###################################################################################
    ########################################################################################
    /**
     * 给予外部调用
     * @param $userId
     */
    public static function outterRegisterNotifyUse($userId){
        try{
            if($userId){
                \App\Service\Share\ShareService::registerNotifyDispatcher($userId);
            }
        }catch (\Exception $e){
            Log::error(__FUNCTION__,[
                'errorMsg'=>$e->getMessage()
            ]);
        }
    }


}