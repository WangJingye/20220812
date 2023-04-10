<?php namespace App\Dlc\Coupon\Service;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

class UserService extends BaseService
{
    private $cacheKey = 'user:new';
    public function addNew($uid){
        $key = "{$this->cacheKey}";
        Redis::sadd($key,$uid);
        return true;
    }

    public function getNew($count = 100){
        $key = "{$this->cacheKey}";
        return Redis::srandmember($key,$count);
    }

    public function removeNew($uids){
        $key = "{$this->cacheKey}";
        if(Redis::exists($key)){
            Redis::srem($key,$uids);
        }
        return true;
    }

}