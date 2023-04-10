<?php

namespace App\Model;

use Illuminate\Support\Facades\Log;
use App\Lib\GuzzleHttp;

class Coupon
{

    static function useCoupon($user_id,$coupon_id)
    {

        $from_params = [
            'coupon_id'=>$coupon_id,
            'uid'=>$user_id,
        ];
        $url = config('api.map')['couponUse'];
        $http_params = [
            'method' => 'post',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params);
        Log::info('useCoupon:request',$http_params);
        Log::info('useCoupon:reponse:'.$content);
        $result = json_decode($content,true);
        if(!$result || !is_array($result)){
            return false;
        }
        return $result;

    }


    static function revertCoupon($user_id,$coupon_id)
    {
        $from_params = [
            'uid'=>$user_id,
            'coupon_id'=>$coupon_id,
        ];
        $url = config('api.map')['couponBack'];
        $http_params = [
            'method' => 'post',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params);
        Log::info('revertCoupon:request',$http_params);
        Log::info('revertCoupon:reponse:'.$content);
        $result = json_decode($content,true);
        if(!$result || !is_array($result)){
            return false;
        }
        return $result;

    }
}
