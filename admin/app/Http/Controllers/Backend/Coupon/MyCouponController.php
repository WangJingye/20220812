<?php namespace App\Http\Controllers\Backend\Coupon;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Arr;

class MyCouponController extends Controller
{   
    public function index(Request $request){
        $uid = $request->get('uid');
        return view('backend.coupon.mycoupon.index',['uid'=>$uid]);
    }

    public function dataList(Request $request){
        $response = $this->curl('inner/couponListByPage',request()->all());
        return $response;
    }


}
