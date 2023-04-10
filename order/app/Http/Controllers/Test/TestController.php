<?php namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use App\Services\Log\Model\Log;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

    }


    public function test(Request $request){
        $config_name = $request->get('config');
        print_r(config($config_name));exit;
//        $api = app('ApiRequestInner',['module'=>'member']);
//        $isMemberInfo = $api->request('user/exist','GET',[
//            'uid'=>1058230,
//        ]);
//        print_r($isMemberInfo);exit;
//        $c = [
//            'coupon_code'=>'',
//            'coupon_id'=>'',
//            'main'=>[
//                ['sku'=>'109100'],
//                ['sku'=>'{{28-2}}{{113001}}{{152103}}'],
//                ['sku'=>'152103']
//            ],
//            'free_try'=>[]
//        ];
//        $a = \App\Repositories\CartRepository::setCheckoutInfo(1,$c);
//        $c = [
//            '109100'=>2,
//            '{{28-2}}{{113001}}{{152103}}'=>4,
//            '152103'=>1,
//        ];
//        $a = \App\Repositories\CartRepository::updateCart(1,$c);
//        var_dump($a);exit;

        $json = '{
    "cartItems":[
        {
            "cart_item_id":101,
            "sku":"20232407795",
          "qty":2,
            "price":2299,
          "unit_price":100,
          "redemption_price":1800,
          "original_price":2299,
            "weight":2.04,
          "is_redemption":0,
            "styleNumber":"20232407795",
            "mid":"GF",
            "cid":"GF",
            "priceType":"X",
            "labourPrice":0,
            "pro_type":"auto",
            "usedPoint":0,
            "discount":0,
            "origin_price":"0.00"
        }     ,
      {
            "cart_item_id":102,
            "sku":"20232407796",
        "qty":2,
        "is_redemption":0,
            "price":2000,
        "unit_price":200,
            "weight":2.04,
            "styleNumber":"gwp_style",
            "mid":"GA",
            "cid":"GA",
            "priceType":"X",
            "labourPrice":0,
            "pro_type":"auto",
            "usedPoint":0,
            "discount":0,
            "origin_price":"0.00"
        }
    ],
    "coupon_id":10,
    "member_coupon_list":"",
    "code":"73KA",
    "is_member":"0",
    "total_points":1110,
  "used_points":100,
    "bestPricePoint":"0",
    "auto":"",
    "combile":"1",
    "page":"order"
}';
        $data = json_decode($json,true);
        /** @var \App\Services\ApiRequest\Inner $api */
        $api = app('ApiRequestInner',['module'=>'goods']);
        $resp = $api->request('outward/product/getProductInfoBySkuIds','POST',['sku_ids'=>'42-2,43-2,44-2,369227,369247','from'=>1]);
        print_r($resp);exit;
        $a = [
            "main_sku"=>[
                "sku"=>'{{套装ID}}{{SKU1}}{{SKU2}}',
                'qty'=>'1'
            ]
        ];
    }

    public function testcart(Request $request){
        \App\Services\WsServices::Notify('9990100101222');exit;


        $cart = new \App\Services\Api\CartServices;
        $items = \App\Repositories\CartRepository::getCart(1);
        $resp = $cart->makeUpCart(1,$items,1);
        print_r($resp);exit;
    }
}
