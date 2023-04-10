<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\PointmallController;
use App\Lib\Http;
use Illuminate\Console\Command;
use App\Model\SocialUsers;
use App\Model\Users;
use App\Model\Address;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
class PointmallCancel extends Command
{

    protected  $day = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pointmall:cancel';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pointmall:cancel';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $this->goods();
        $this->coupon();
        $this->info('处理完成:'.date("Y-m-d H:i:s"));

    }

    public function goods(){
        $end=date('Y-m-d H:i:s',strtotime('-'.$this->day.' day'));
        $where=[
            ['wbs_user_point.status',PointmallController::STATUS_ACTIVE],
            ['wbs.type','product'],
            ['wbs_user_point.created_at','<',$end]
        ];
        $list= DB::table("wbs_user_point")
            ->select('wbs.type','wbs.product_sku','wbs.exchange_point','wbs_user_point.id','wbs_user_point.channel_id','wbs_user_point.status','wbs_user_point.created_at')
            ->join('wbs','wbs.id','=','wbs_user_point.wbs_id')
            ->where($where)
            ->orderBy('wbs_user_point.created_at','desc')
            ->get();
        $list->map(function ($item){
            //dd($item);
            DB::table('wbs_user_point')->where([
                ["id",$item->id]
            ])->update([
                'status'=>PointmallController::STATUS_EXPIRE
            ]);
            $this->doGoodStock($item->product_sku,$item->channel_id);

        });
    }

    public function coupon(){
        $end=date('Y-m-d H:i:s',strtotime('-'.$this->day.' day'));
        $where=[
            ['wbs_user_point.status',PointmallController::STATUS_ACTIVE],
            ['wbs.type','coupon'],
            ['wbs_user_point.created_at','<',$end]
        ];
        $list= DB::table("wbs_user_point")
            ->select('wbs.type','wbs.coupon_id','wbs.exchange_point','wbs_user_point.id','wbs_user_point.user_id','wbs_user_point.status','wbs_user_point.created_at')
            ->join('wbs','wbs.id','=','wbs_user_point.wbs_id')
            ->where($where)
            ->orderBy('wbs_user_point.created_at','desc')
            ->get();


        $list->map(function ($item){
            $myCoupon=DB::table('tb_user_coupon')->where([
                ["coupon_id",$item->coupon_id],
                ["user_id",$item->user_id]
            ])->whereNull('used_at')
                ->get();
           // dd($myCoupon);
            //处理过期
        });

    }

    function doGoodStock($sku,$channel){
        $prams=['sku_json'=>json_encode([[$sku,1,'积分商城过期退库存']]),'channel_id'=>$channel,'increment'=>1];
        $http= new Http();
        $convertApi=$http->curl('outward/update/batchStock',$prams);
        loger([
            'request'=>$prams,
            'response'=>$convertApi
        ],'batchStock');
        return $convertApi;
    }
}