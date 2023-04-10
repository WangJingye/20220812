<?php

namespace App\Http\Controllers\Backend\Coupon;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\DB;


class CouponSendController extends Controller
{
    public function edit(Request $request){
        $id= $request->get('id');
        return view('backend.coupon.couponsend.edit',['id'=>$id]);
    }

    public function update(){
        $response = $this->curl('inner/couponSend',request()->all());
        return $response;
    }

}
