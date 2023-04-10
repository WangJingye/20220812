<?php

namespace App\Console\Commands;

use App\Model\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Model\Users;
use App\Model\MemberMergeRecord;
use App\Service\Dlc\UsersService;
use App\Service\Dlc\Sftp;
use Illuminate\Support\Facades\Redis;

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

    protected function cancelmember($mobile){
        $user = Users::query()->where('phone',$mobile)->first();
        if($user->pos_id){
            $resp = (new \App\Service\DLCCrm\Request)->cancelMember([
                'memberCode'=>$user->pos_id,
            ]);
            if($resp===true){
                $this->delmember($user->id);
                $this->line('解绑成功');
            }else{
                $this->line('解绑失败');
            }
        }else{
            $this->line('未入会');
        }
    }

    protected function delmember($uid){
        Users::query()->where('id',$uid)->delete();
        Redis::hdel(config('app.name').':member:pos_id',$uid);
        Redis::del('refresh.token.'.$uid.'miniapp');
        Redis::del('token.'.$uid.'miniapp');
    }

    protected function changemember($uid){
        Users::query()->where('id',$uid)->update(['pos_id'=>'']);
        Redis::hdel(config('app.name').':member:pos_id',$uid);
    }

    protected function makeuser($count){
        $count = $count?intval($count):1;
        for($i=0;$i<$count;$i++) {
            $phone = "158".rand(10000000,99999999);
            Users::query()->insert([
                'source_type'=>2,
                'channel'=>3,
                'birth'=>'1979-07-06',
                'phone'=>$phone,
                'name'=>'test'.date('mdHis').$i,
                'open_id'=>uniqid(),
            ]);
        }
    }

    private function redisdelete($key){
        $keys = [];$it = 0;
        while (true) {
            $scan = Redis::scan($it,'match',$key,'count',100);
            $it = $scan[0];
            if($scan[1])$keys =  array_merge($keys,$scan[1]);
            if(!$it)break;
        }
        if(sizeof($keys)){
            foreach($keys as $k=>$v){
                Redis::del($v);
            }
        }
    }

}
