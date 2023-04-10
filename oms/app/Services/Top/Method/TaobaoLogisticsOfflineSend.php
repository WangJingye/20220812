<?php namespace App\Services\Top\Method;

use App\Services\Top\TopAbstract;

class TaobaoLogisticsOfflineSend extends TopAbstract
{
    /**
     * 线下物流
     * @return array
     * @throws \Exception
     */
    public function execute():array
    {
        $tid = $this->request->get('tid');
        $company_code = $this->request->get('company_code');
        $out_sid = $this->request->get('out_sid');
        //子订单号 拆单用
        $sub_tid = $this->request->get('sub_tid');

        $api = app('ApiRequestMagento');
        $params = [
            'tracks'=>[
                [
                    'track_number'=>$out_sid,
                    'title'=>$company_code,
                    'carrier_code'=>$company_code
                ]
            ]
        ];
        //组装子订单号
        if($sub_tid){
            $sub_tid_arr = explode(',',$sub_tid);
            $params['items'] = array_reduce($sub_tid_arr,function($result,$item){
                $result[] = [
                    'order_item_id' => $item
                ];
                return $result;
            });
        }

//        $resp = $api->exec(['url'=>"V1/connextOrder/{$tid}/ship",'method'=>'POST'],$params);

        $result = isset($resp->message)?false:true;
        return [
            'logistics_offline_send_response'=>[
                'shipping'=>[
                    'is_success'=>[],
                ]
            ]
        ];
    }
}
