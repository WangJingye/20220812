<?php

namespace App\Dlc\Coupon\Commands;

use App\Dlc\Coupon\Service\CouponService;
use App\Model\Promotion\Cart;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShellCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupon:shell {option} {params?}';

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
            $this->info($e->getMessage() . ' Method not found！');
        }
        $this->line('All done');
    }

    protected function test($params)
    {
        $this->line('this is test');
    }

    public function cacheallcarts(){
        $carts = Cart::get();
        $couponService = CouponService::getInstance();
        foreach($carts as $cart){
            $couponService->setCache($cart->id,$cart->toArray());
        }
    }

    protected function updatesql(){
        $sql = <<<SQL
Alter table promotion_cart add column is_new tinyint(1) comment '是否是新人券';
SQL;
        DB::statement($sql);

        $sql = <<<SQL
Alter table promotion_cart add column notice text comment '使用须知';
SQL;
        DB::statement($sql);

        $sql = <<<SQL
Alter table promotion_cart add column trigger_sku varchar(50) comment '自动触发SKU';
SQL;
        DB::statement($sql);
    }
}
