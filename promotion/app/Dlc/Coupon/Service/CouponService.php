<?php namespace App\Dlc\Coupon\Service;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

class CouponService extends BaseService
{
    private $cacheKey = 'promotion:cart';
    public function setCache($id,$data){
        $key = "{$this->cacheKey}:{$id}";
        Redis::hmset($key,$data);
        return true;
    }

    public function getCache($id){
        $key = "{$this->cacheKey}:{$id}";
        return Redis::hgetall($key);
    }

    public function delCache($id){
        $key = "{$this->cacheKey}:{$id}";
        Redis::del($key);
        return true;
    }














}