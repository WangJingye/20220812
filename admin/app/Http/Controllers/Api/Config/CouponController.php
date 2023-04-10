<?php

namespace App\Http\Controllers\Api\Config;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Controller;
use App\Model\Config;
use App\Lib\Http;

class CouponController extends Controller
{
    //新客优惠券
    public function newMemberCoupon(){
        $new_member_coupon = Config::where('config_name','new_member_coupon')->get()->toArray();
        $new_member_coupon_pic = Config::where('config_name','new_member_coupon_pic')->get()->toArray();
        $response['code'] = 1;
        $response['msg'] = "新客优惠券";
        $response['data'] = [
            'couponId'=>$new_member_coupon[0]['config_value'],
            'popBg'=>env('OSS_DOMAIN').$new_member_coupon_pic[0]['config_value'],//url
        ];
        //获取coupon信息
        $coupon_id = $response['data']['couponId'];
        if(!$coupon_id){
            return [];
        }
        $http = new Http();
        $item = $http->curl('promotion/coupon/dataList',[$coupon_id]);
        if(!isset($item['data'][0])){
            return [];
        }
        $item = $item['data'][0];
        $expire_flag = 0;
        if( time() > strtotime($item['end_time']) ){
            $expire_flag = 1;
        }
        $data = [
                'id'=>$item['id'],
                'status'=>$expire_flag,//0:未过期，1:已过期
                'price'=>$item['total_discount'],
                'require'=>$item['total_amount'],
                'start'=>$item['start_time'],
                'end'=>$item['end_time'],
                ];
        return $data;
    }
}
