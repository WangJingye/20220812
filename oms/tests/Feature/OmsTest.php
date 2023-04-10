<?php

namespace Tests\Feature;

use App\Services\Dlc\SalesServices;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OmsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
//        $resp = (new \App\Services\DLCOms\Request)->orderAdd(['a'=>1]);
        $params = ['skus'=>['skutest001']];

        $skus = $params['skus'];
        $date = date('Y年m月d日');
        $subscribe = new \App\Model\SubscribeShipped;
        $resp = app('ApiRequestInner')->request('outward/product/getProductInfoBySkuIds','POST',[
            'sku_ids'=>implode(',',$skus),
        ]);
        if($resp['code']!=1){
            throw new \Exception('获取商品错误');
        }
        $products = $resp['data'];
        $goods = [];
        foreach($products as $sku => $product){
            $goods[$sku] = [
                'spu'=>$product['id'],
                'name'=>$product['product_name'],
                'spec'=>$product['sku']['spec_desc'],
                'amount'=>$product['default_ori_price'],
                'date'=>$date,
            ];
        }
        foreach($skus as $sku){
            $reminders = SalesServices::getArrivalReminders($sku);
            if($reminders && count($reminders)){
                foreach($reminders as $open_id=>$data){
                    $data = json_decode($data,true);
                    $state = array_get($data,'state');
                    $template_id = array_get($data,'template_id');
                    if(array_key_exists($sku,$goods)){
                        $subscribe->arrivalMessage($goods[$sku],$open_id,$template_id,$state);
                    }
                    SalesServices::hdelArrivalReminder($sku,$open_id);
                }
            }
        }


        $this->assertTrue(true);
    }
}
