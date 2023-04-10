<?php

namespace App\Dlc\Coupon\Commands;

use App\Dlc\Coupon\Model\Coupons;
use App\Dlc\Coupon\Service\{CouponService,UserService,PromotionService,HelperService};
use App\Model\Users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Validator;

class ShellCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupon:shell {option} {--mobiles=}{--mobile=}{--uids=}{--id=}{--day=}{params?}';

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

    public function handle()
    {
        try {
            $option = $this->argument('option');
            $params = $this->argument('params');
            $params = $params ?? '';
            call_user_func_array([__CLASS__, $option], [$params]);
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
        $this->line('All done');
    }

    protected function test($params)
    {
        $this->line('this is test');
    }

    protected function couponget(){
        $v = Validator::make($this->options(), [
            'id' => 'required',
            'mobiles' => 'required',
        ], [
            'required' => ':attribute是必填项',
        ]);
        if ($v->fails()) {
            throw new \Exception($v->errors()->first());
        }

        $couponId = $this->option('id');
        $mobiles = explode(',',$this->option('mobiles'));
        $userIds = Users::whereIn('phone',$mobiles)->pluck('id');
        if(empty($userIds)){
            throw new \Exception('未找到相应用户');
        }
        $userIds = $userIds->toArray();
        /** @var \App\Dlc\Coupon\Service\CouponService $couponService */
        $couponService = CouponService::getInstance();
        $res = $couponService->couponUseHandle($couponId,$userIds);
        $this->line($res?'操作成功':'操作失败');
    }

    /**
     * 发送新人券
     */
    protected function sendnewcoupon(){
        try{
            $userService = UserService::getInstance();
            $uids = $userService->getNew();
            /** @var \App\Dlc\Coupon\Service\CouponService $couponService */
            $couponService = CouponService::getInstance();
            $promotion = $couponService->getPromotionNewDetail();
            if(!empty($promotion)){
                $res = $couponService->couponUseHandle($promotion['id'],$uids);
                if($res){
                    $userService->removeNew($uids);
                }else{
                    throw new \Exception('优惠券发送失败');
                }
            }else{
                //未配置促销则清空新人标识
                $userService->removeNew($uids);
                throw new \Exception('未配置新人礼促销');
            }
        }catch (\Exception $e){
            $errMsg = $e->getMessage();
            $this->line($errMsg);
        }
        HelperService::getInstance()->log('sendnewcoupon',[
            'code'=>isset($errMsg)?0:1,
            'uids'=>$uids,
            'errMsg'=>$errMsg??'',
        ]);
    }

    protected function couponback(){
        $couponId = $this->option('id');
        $mobile = $this->option('mobile');
        $user = Users::where('phone',$mobile)->first();
        if(empty($user)){
            throw new \Exception('用户不存在');
        }
        $couponService = CouponService::getInstance();
        $res = $couponService->couponBack($user->id,$couponId);
        $this->line($res?'返还成功':'返还失败');
    }

    /**
     * 优惠券发放脚本
     * @throws \Exception
     */
    protected function orderTriggerCouponSend(){
        $currDate = date('Y-m-d H:i:s');
        $day = is_null($this->option('day'))?'1':$this->option('day');
        $start_time = date('Y-m-d 00:00:00',strtotime("-{$day} days"));
        $end_time = date('Y-m-d 23:59:59',strtotime("-{$day} days"));
        $promotions = DB::connection('promotion')->table('promotion_cart')
            ->where('status',2)
            ->where('start_time','<=',$currDate)
            ->where('end_time','>',$currDate)
            ->where('is_new',0)
            ->whereIn('type',['coupon','product_coupon'])
            ->whereNotNull('trigger_sku')
            ->get()
        ;
        /** @var \App\Dlc\Coupon\Service\CouponService $couponService */
        $couponService = CouponService::getInstance();
        $helperService = HelperService::getInstance();
        foreach($promotions as $promotion){
            if($promotion->trigger_sku){
                $trigger_sku_arr = explode(',',$promotion->trigger_sku);
                $users = DB::connection('oms')->table('oms_order_main')
                    ->join('oms_order_items','oms_order_main.id','=','oms_order_items.order_main_id')
                    ->where('oms_order_main.created_at','>=',$start_time)
                    ->where('oms_order_main.created_at','<',$end_time)
                    ->where('oms_order_main.order_status','>=',3)
                    ->whereIn('oms_order_items.sku',$trigger_sku_arr)
                    ->pluck('oms_order_main.user_id')->unique();
                if($users->count()){
                    $userIds = $users->toArray();
                    $res = $couponService->couponUseHandle($promotion->id,$userIds);
                    $helperService->log(__FUNCTION__,[
                        'couponId'=>$promotion->id,
                        'trigger_sku'=>$promotion->trigger_sku,
                        'userIds'=>$userIds,
                        'status'=>$res?1:0,
                    ]);
                    $this->line('优惠券编号:'.$promotion->id.'----'.($res?'发放成功':'发放失败'));
                }
            }
        }

    }

    protected function updatesql(){
        $sql = <<<SQL
CREATE TABLE `coupons` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `coupon_id` int(10) NOT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0未核销 1已核销',
  `type` varchar(50) COLLATE utf8mb4_bin NOT NULL COMMENT '1满减券 2实物券',
  `is_new` tinyint(1) DEFAULT '0' COMMENT '是否是新客券',
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uuid` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
SQL;
        DB::statement($sql);

        $sql = <<<SQL
ALTER TABLE tb_users ADD COLUMN reg_time DATETIME COMMENT '注册时间';
SQL;
        DB::statement($sql);

    }



}
