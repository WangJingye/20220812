<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use App\Service\Share\ShareService as Service;
use App\Exceptions\EventExpireException;

class ShareQueued implements ShouldQueue
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
        echo $res;
        Log::info('ShareQueued',[
            'method'=>$this->method,
            'params'=>$this->params,
            'res'=>$res,
        ]);
    }

    /**
     * 失败队列处理(已达到最大尝试次数)
     */
    public function failed(){
        //
    }

    protected function payNotify($params){
        $result = (new Service)->handlePayNotify($params['userId'],$params['orderId']);
        return $result===true?'执行成功':$result;
    }

    protected function registerNotify($params){
        $result = (new Service)->handleRegisterNotify($params['userId']);
        return $result===true?'执行成功':$result;
    }

}
