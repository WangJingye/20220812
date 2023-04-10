<?php

namespace App\Model;

use App\Model\Common\RedisBase;
use think\facade\Config;

class RedisModel extends RedisBase
{
    public function __construct()
    {
        $this->init(config('redis.REDIS_HOST'), config('redis.REDIS_PORT'), config('redis.REDIS_TIMEOUT'), config('redis.REDIS_AUTH'), 1);
    }
}
