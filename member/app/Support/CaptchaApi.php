<?php

namespace App\Support;

use Illuminate\Support\Facades\Redis;

class CaptchaApi
{
    public static function getCaptcha($config = 'default')
    {
        $data = app('captcha')->create($config, true);
        Redis::set('captcha_api_' . $data['key'], 1, 'EX', 60 * 60);
        return $data;
    }

    public static function checkCaptch($captcha,$key){
        $captcha = strtolower($captcha);
        if (!Redis::get('captcha_api_' . $key)) {
            return -1;
        }
        return captcha_api_check($captcha, $key);
    }
}

