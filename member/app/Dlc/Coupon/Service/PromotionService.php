<?php namespace App\Dlc\Coupon\Service;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

class PromotionService extends BaseService
{
    private $cacheKey = 'promotion:cart';
    public function getDetail($id){
        $key = "{$this->cacheKey}:{$id}";
        return Redis::hgetall($key);
    }

    public function getDetails($ids){
        $list = Redis::pipeline(function($pipe) use($ids) {
            $list = [];
            foreach($ids as $id){
                $key = "{$this->cacheKey}:{$id}";
                $list[] = $pipe->hgetall($key);
            }
            return $list;
        });
        return array_combine(array_column($list,'id'),$list);
    }

}