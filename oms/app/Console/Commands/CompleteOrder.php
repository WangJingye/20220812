<?php

namespace App\Console\Commands;

use App\Model\Sku;
use Illuminate\Console\Command;
use App\Model\Order;

class CompleteOrder extends Command
{
    protected $signature = 'run:completeOrder';

    protected $description = '订单完成';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $order = new Order;
            //更新完成订单
            $over_date = date("Y-m-d H:i:s", strtotime("-15 days"));

            $data = $order::select('order_sn','id', 'channel','payment_type','mobile','contact','coupon_id','user_id')->where('channel','!=', 0)->where('order_status', 9)->where('received_at', '<', $over_date)->limit(500)->get()->toArray();

            foreach ($data as $v) {
                Order::completeOrder($v['id']);
            }
            $sku = new Sku;
            $data = $order::select('id', 'pos_id')->where('id','>=',200113)->whereNull('level')->where('channel','!=', 0)->limit(100)->get()->toArray();
            foreach ($data as $v) {
               $info = $sku->getUserLevel($v['pos_id']);
               if(!empty($info)){
                   Order::where('id',$v['id'])->update(
                       [
                           'level'=>$info['CustomerType']
                       ]
                   );
               }
            }


        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

}