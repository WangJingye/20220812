<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class ApiQueued implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $method;
    protected $params;

    /**
     * Create a new job instance.
     * @param $method
     * @param $params
     */
    public function __construct($method,$params){
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle(){
        $res = call_user_func([$this,$this->method],$this->params);
        if($res){
            echo "$res\n\r";
        }else{
            echo "执行成功\n\r";
        }
    }

    /**
     * 失败队列处理(已达到最大尝试次数)
     */
    public function failed(){
        //
    }

    /**
     * 点赞并且增加积分
     */
    protected function collectNews($params){
        try{
            $result = DB::transaction(function() use($params){
                $aid = $params['aid'];
                $mix_nick = $params['mix_nick'];
                if(DB::table('bb_like')->where($params)->count()){
                    throw new \Exception('已经存在');
                }
                $userModel = DB::table('bb_user_nick')->where('mix_nick',$mix_nick);
                if($userModel->count()) {
                    //增加点赞记录
                    if(DB::table('bb_like')->insert($params)){
                        //增加相应测评点赞数量
                        if(DB::table('page')->where('id',$aid)->increment('like_count')){
                            //增加积分
                            $type = 1;//分值对应的类型
                            $current_date = date('Ymd');
                            $points = config('arden.points_type')[$type];
                            $user = $userModel->first();
                            $old_points = $user->day_points;
                            //如果已过期则积分重新计算
                            if($user->day_points_time<$current_date){
                                $old_points = 0;
                            }
                            if(($old_points+$points)<=config('arden.max_day_points')){
                                //满足条件增加积分
                                if(DB::table('bb_integral')->insert(['mix_nick'=>$mix_nick,'points'=>$points,'type'=>$type])
                                    &&$userModel->update(['day_points'=>($old_points+$points),'day_points_time'=>$current_date])){
                                    //调用CRM接口增加积分
                                    if(app()->make(\App\Lib\Crm\Crm::class)->like($mix_nick)
                                    &&$userModel->update(['crm_send'=>1])){
                                        return '执行成功';
                                    }
                                    throw new \Exception('CRM积分增加失败');
                                }
                                throw new \Exception('本地积分增加失败');
                            }
                            return '执行成功,但未增加积分';
                        }
                    }
                }else{
                    throw new \Exception('找不到相应用户');
                }
                throw new \Exception('异常');
            });
            $msg = $result;
        }catch (\Exception $e){
            $msg = $e->getMessage();
        }
        $log = [
            'RequestData'=>$params,
            'RespondData'=>$msg,
        ];
        log_json('mq',__FUNCTION__,json_encode($log,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    /**
     * 分享并且增加积分
     */
    protected function share($params){
        try{
            $result = DB::transaction(function() use($params){
                $mix_nick = $params['mix_nick'];
                $userModel = DB::table('bb_user_nick')->where('mix_nick',$mix_nick);
                if($userModel->count()) {
                    //增加分享记录
                    if(DB::table('bb_share')->insert($params)){
                        //增加积分
                        $type = 3;//分值对应的类型
                        $current_date = date('Ymd');
                        $points = config('arden.points_type')[$type];
                        $user = $userModel->first();
                        $old_points = $user->day_points;
                        //如果已过期则积分重新计算
                        if($user->day_points_time<$current_date){
                            $old_points = 0;
                        }
                        if(($old_points+$points)<=config('arden.max_day_points')){
                            //满足条件增加积分
                            if(DB::table('bb_integral')->insert(['mix_nick'=>$mix_nick,'points'=>$points,'type'=>$type])
                                &&$userModel->update(['day_points'=>($old_points+$points),'day_points_time'=>$current_date])){
                                //调用CRM接口增加积分
                                if(app()->make(\App\Lib\Crm\Crm::class)->share($mix_nick)
                                &&$userModel->update(['crm_send'=>1])){
                                    return '执行成功';
                                }
                                throw new \Exception('CRM积分增加失败');
                            }
                            throw new \Exception('本地积分增加失败');
                        }
                        return '执行成功,但未增加积分';
                    }
                }else{
                    throw new \Exception('找不到相应用户');
                }
                throw new \Exception('异常');
            });
            $msg = $result;
        }catch (\Exception $e){
            $msg = $e->getMessage();
        }
        $log = [
            'RequestData'=>$params,
            'RespondData'=>$msg,
        ];
        log_json('mq',__FUNCTION__,json_encode($log,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    /**
     * 评论并且增加积分
     */
    protected function comment($params){
        try{
            $result = DB::transaction(function() use($params){
                $mix_nick = $params['mix_nick'];
                $userModel = DB::table('bb_user_nick')->where('mix_nick',$mix_nick);
                if($userModel->count()) {
                    //增加积分
                    $type = 2;//分值对应的类型
                    $current_date = date('Ymd');
                    $points = config('arden.points_type')[$type];
                    $user = $userModel->first();
                    $old_points = $user->day_points;
                    //如果已过期则积分重新计算
                    if($user->day_points_time<$current_date){
                        $old_points = 0;
                    }
                    if(($old_points+$points)<=config('arden.max_day_points')){
                        //满足条件增加积分
                        if(DB::table('bb_integral')->insert(['mix_nick'=>$mix_nick,'points'=>$points,'type'=>$type])
                            &&$userModel->update(['day_points'=>($old_points+$points),'day_points_time'=>$current_date])){
                            //调用CRM接口增加积分
                            if(app()->make(\App\Lib\Crm\Crm::class)->share($mix_nick)
                            &&$userModel->update(['crm_send'=>1])){
                                return '执行成功';
                            }
                            throw new \Exception('CRM积分增加失败');
                        }
                        throw new \Exception('本地积分增加失败');
                    }
                    return '执行成功,但未增加积分';
                }
                throw new \Exception('找不到相应用户');
            });
            $msg = $result;
        }catch (\Exception $e){
            $msg = $e->getMessage();
        }
        $log = [
            'RequestData'=>$params,
            'RespondData'=>$msg,
        ];
        log_json('mq',__FUNCTION__,json_encode($log,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }

    protected function test($params){
        return true;
    }
}
