<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Model\Promotion\Cart;
use App\Service\Redis\Keys;

//同步所有的status=2,3的promotion到redis缓存
class SyncPromotionToCacheCommand extends Command
{
    protected $signature = 'syncPromotion';

    protected $description = 'sync active/un-active promotion to cache';

    public function __construct(){
        parent::__construct();
    }

    public function handle()
    {
        $rules = Cart::whereIn('status',[2,3])->get()->toArray();
        $data = [];
        foreach($rules as $item){
            $rule_id = $item['id'];
            $data[$rule_id] = json_encode($item);
        }
        $redis_key = Keys::getRuleKeys();
        Redis::HMSET($redis_key,$data);
    }

}