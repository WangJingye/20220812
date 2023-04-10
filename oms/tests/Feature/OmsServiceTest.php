<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Curl\Curl;

class OmsServiceTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $curl = new Curl;
        $headers = [
            'Content-type'=>'application/x-www-form-urlencoded',
            'charset'=>'utf-8',
        ];
        $curl->setHeaders($headers);
        $curl->setOpts([
            CURLOPT_SSL_VERIFYPEER=>0,
            CURLOPT_SSL_VERIFYHOST=>0,
        ]);
        $curl->setConnectTimeout(10);

        $url = env('APP_URL').'/Service/Oms';

//        $data = $this->updateOrderStatus($url);
        $data = $this->syncGoodsPrice($url);
//        $data = $this->syncGoodsStock($url);
//        $data = $this->sendInvoice($url);
        $data['sign'] = $this->getSignature($data);
        call_user_func([$curl,'POST'],$url,$data);
        $resp = $curl->response;
        print_r($resp);
        print_r($curl->errorMessage);
        $this->assertTrue(true);
    }

    private function updateOrderStatus(&$url){
        $url = $url.'/LvmhSiteUpdateOrderStatus';
        $data = [
            'app_key'=>'dlc',
            'timestamp'=>time(),
            'version'=>'1.0',
            'random'=>$this->getRandom(),
            'format'=>'json',
        ];
        $params = [
            'order_bn'=>'2708282300000002',
            'status'=>'synced',
            'logi_bn'=>'',
            'item_info'=>[],
            'bill_info'=>[],
        ];
        $data['params'] = json_encode($params);
        return $data;
    }

    private function syncGoodsPrice(&$url){
        $url = $url.'/LvmhSiteSyncGoodsPrice';
        $data = [
            'app_key'=>'dlc',
            'method'=>'lvmh.site.sync.goods.price',
            'timestamp'=>time(),
            'version'=>'1.0',
            'random'=>$this->getRandom(),
            'format'=>'json',
        ];
        $params_json = '{"26002":"1150.000","22005":"880.000","22004":"1365.000","22002":"1150.000","21020":"380.000","21025":"880.000","21002":"1150.000","21001":"870.000","025":"420.000","A25022":"900.000","A25021":"900.000","009":"1150.000","25034":"1780.000","27052":"1680.000","95325":"2950.000","95322":"960.000","95323":"1350.000","95324":"1350.000","486":"700.000","24009":"2000.000","44082":"480.000","40053":"1820.000","47048":"1820.000","49040":"1820.000","49902":"1490.000","25035":"1345.000","27005":"510.000","57024":"460.000","57023":"460.000","A57022":"470.000","57021":"460.000","A57020":"470.000","57027":"565.000","57019":"460.000","57018":"460.000","57017":"1305.000","57016":"870.000","A57015":"1340.000","57014":"870.000","57010":"1305.000","57009":"870.000","57008":"1305.000","A57007":"890.000","57006":"1305.000","A57005":"890.000","57111":"330.000","57004":"1305.000","57003":"870.000","57002":"1305.000","57001":"865.000","458":"760.000","456":"760.000","452":"760.000","450":"760.000","433":"1635.000","432":"1635.000","431":"1635.000","430":"1635.000","076":"710.000","075":"1020.000","420":"955.000","44081":"480.000","47113":"0.000","40101":"0.000","95043":"1730.000","95042":"1180.000","95041":"1180.000","25039":"580.000","57228":"0.000","27100":"0.000","25058":"700.000","24301":"0.000","57256":"0.000","24440":"0.000","24430":"0.000","57325":"175.000","57326":"175.000","57327":"175.000","57328":"175.000","57329":"175.000","57315":"215.000","57316":"215.000","57317":"215.000","57318":"215.000","57319":"215.000","57300":"420.000","57301":"420.000","57302":"420.000","57303":"420.000","57304":"420.000"}';
        $params = json_decode($params_json,true);
//        $params = [
//            '152510'=>'166.00',
//            '103200'=>'266.00',
//        ];
        $data['params'] = json_encode($params);
        return $data;
    }

    private function syncGoodsStock(&$url){
        $url = $url.'/LvmhSiteSyncGoodsStock';
        $data = [
            'app_key'=>'dlc',
            'timestamp'=>time(),
            'version'=>'1.0',
            'random'=>$this->getRandom(),
            'format'=>'json',
        ];
        $params = [
            '57029'=>999,
            'A57007'=>999,
            '57008'=>999,
            'A57110'=>999,
            '57111'=>999,
            '57114'=>999,
            '57117'=>999,
            '57113'=>999,
            'A57112'=>999,
            '57118'=>999,
            '81000'=>999,
            'A81001'=>999,
            '47024'=>999,
            'A49023'=>999,
            '40023'=>999,
            'LH100001'=>999,
            '47023'=>999,
        ];
        $data['params'] = json_encode($params);
        return $data;
    }

    private function sendInvoice(&$url){
        $url = $url.'/LvmhSiteSendInvoice';
        $data = [
            'app_key'=>'dlc',
            'timestamp'=>time(),
            'version'=>'1.0',
            'random'=>$this->getRandom(),
            'format'=>'json',
        ];
        $params = [
            'order_bn'=>'2508282300000002',
            'invoice_id'=>'111',
            'pdf_url'=>'222',
            'invoice_code'=>'333',
            'invoice_no'=>'444',
        ];
        $data['params'] = json_encode($params);
        return $data;
    }

    private function getSignature($params){
        $app_key = config('dlc.dlc_oms_app_key');
        $app_secret = config('dlc.dlc_oms_app_secret');
        $params['app_key'] = $app_key;

        ksort($params);
        $sign='';
        foreach($params as $k=>$v){
            $sign.=$k.'='.$v.'&';
        }
        return strtoupper(md5($sign.$app_secret));
    }

    private function getRandom($len = 16){
        $returnStr='';
        $pattern = '1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        for($i = 0; $i < $len; $i ++) {
            $returnStr .= $pattern {mt_rand ( 0, 35 )};
        }
        return $returnStr;
    }
}
