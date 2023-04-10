<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Curl\Curl;

class TestShell extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:shell {option} {params?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $option = $this->argument('option');
            $params = $this->argument('params');
            call_user_func_array([__CLASS__, $option], [$params]);
            $this->line('['.date('Y-m-d H:i:s').']Successful');
        } catch (\Exception $e) {
            $this->line($e->getMessage());
        }$this->line('['.date('Y-m-d H:i:s').']End');
    }

    protected function demo($times=3){
        if($times==0){
            while(1){
                $start = $this->get_millisecond();
                $result = $this->demoHandle();
                $use_time = $this->get_millisecond()-$start;
                $this->line("time:{$use_time}ms");
                sleep(1);
            }
        }else{
            for($i=0;$i<$times;$i++){
                $start = $this->get_millisecond();
                $result = $this->demoHandle();
                $use_time = $this->get_millisecond()-$start;
                $this->line("time:{$use_time}ms");
            }
        }
        return;
    }

    protected function demoHandle(){
        $curr_time = date('Y-m-d H:i:s');
        $where = [
            ['status','=',2,],['start_time','<',$curr_time],['end_time','>',$curr_time],//激活，有效的
        ];
        $result = DB::table('free_trial')->where($where)->orderBy('id','desc')->get();
        return $result;
    }

    protected function get_millisecond(){
        $time = explode(" ", microtime());
        return intval(($time[1]+$time[0])*1000);
    }

    protected function curltest($url){
        while(1){
            $start = $this->get_millisecond();
            $result = $this->docurltest($url);
            $use_time = $this->get_millisecond()-$start;
            $date = date('Y-m-d H:i:s');
            $str = "[{$date}]{$result},time:{$use_time}ms";
            $log = ['result'=>$str];
            if($use_time>=1000 && $use_time<2000){
                $log['status'] = 'warning';
            }elseif($use_time>=2000){
                $log['status'] = 'alert';
            }
            log_json('curltest',$log);
            $this->line($str);
            sleep(1);
        }
        return;
    }

    protected function docurltest($url){
        try{
            $curl = new Curl();
            $curl->setHeader('Content-Type','application/json');
            $curl->setConnectTimeout(10);

            $curl->setOpt(CURLOPT_SSL_VERIFYPEER,0);
            call_user_func([$curl,'POST'],$url);
            $resp = $curl->getRawResponse();
            if($resp){
                $resp = json_decode($resp);
                if($resp->code != 1){
                    throw new \Exception($resp->message);
                }
            }else{
                throw new \Exception('没有返回值');
            }
            if($curl->error){
                throw new \Exception($curl->errorMessage);
            }
            return 'SuccessFull';
        }catch(\Throwable $e){
            log_array('curltestError',[
                'error'=>$e->getMessage()
            ]);
            return $e->getMessage();
        }
    }

}
