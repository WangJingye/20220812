<?php namespace App\Services\Checkout\Depend;

use App\Services\Api\TrialServices;

//付邮试用活动的信息, 付邮活动的信息在同一个模块，直接调用service class
class ShipfeeTry
{
    public $api = '';

    public function getCampaignInfo($data_obj){
        /** @var \App\Services\Checkout\ShipfeeTry\Data $data_obj */
        $data = $data_obj->getData();
        $campaing_id = $data['ship_fee_try_campaign_id'];
        $params = ['id'=>$campaing_id];
        $resp = TrialServices::getList($params);
        $resp = json_decode(json_encode($resp),true);
        $flag = $this->validateInput($resp,$data);
        if($flag != 1){
            return $flag;
        }
        $data_obj = $data_obj->setShipfeeTryInfo($resp);
        return $data_obj;
    }

    //用户输入的skus, campaign id要在系统里面存在
    //选择的数量也需要符合
    //不能选择货到付款
    public function validateInput($resp,$data){
        $response_code = $resp['code'];
        if($response_code == 0){
            return -1;
        }
        $trial_data = $resp['data'][0];
        $skus = $trial_data['add_sku'];
        $skus_arr = explode(',',$skus);
        $input_skus = $data['ship_fee_try_skus'];
        foreach ($input_skus as $item){
            if(!in_array($item['sku'],$skus_arr)){//输入的sku不在付邮配置的skus里面
                return -3;
            }
        }
        if($data['payment_method'] and $data['payment_method'] == 'Offline'){//付邮试用不能选择货到付款
            return -4;
        }
        return 1;
    }

}
