<?php

namespace App\Support;

use Illuminate\Support\Facades\Redis;

class CaptchaApi
{
    public static function getCaptcha($config = 'default')
    {
        $data = app('captcha')->create($config, true);
        Redis::set('captcha_api_' . $data['key'], 1, 'EX', 60 * 3);
        return $data;
    }

    public static function checkCaptch($captcha,$key){
        if (!Redis::get('captcha_api_' . $key)) {
            return false;
        }
        return captcha_api_check($captcha, $key);
    }
}

