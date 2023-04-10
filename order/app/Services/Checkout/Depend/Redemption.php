<?php
namespace App\Services\Checkout\Depend;

//积分兑换的商品
//@DEPREATED,这个已经弃用，使用实物券替换
class Redemption
{
    public $api = 'pointmall/my/convert';

    //获取积分兑换的商品
    public function getMyRedemptionList($data_obj){
        $data = $data_obj->getData();
        $customer_id = $data['customer_id'];
        $api = app('ApiRequestInner',['module'=>'member']);
        $input_data = ['page'=>'checkout','user_id'=>$customer_id];
        $resp = $api->request($this->api,'POST',$input_data);
        $redemption_list = $resp['data']['list'];
        /** @var \App\Services\Checkout\Data\Data $data_obj */
        $data_obj->setRedemptionList($redemption_list);
        return $data_obj;
    }

    //创建订单的时候的处理
    public function processOnCreateOrder($data_obj){
        $data_obj = $this->getMyRedemptionList($data_obj);
        $validate_flag = $this->validateInputRedemptionIds($data_obj);
        if($validate_flag == false){
            return false;
        }
        $data_obj = $data_obj->setSelectedRedemptionList();
        return $data_obj;
    }

    //检测提交的兑换商品必须要在我的兑换商品列表里面
    public function validateInputRedemptionIds($data_obj){
        $data = $data_obj->getData();
        $redemption_list = $data['redemption_list'];
        $input_selected_redemption_ids = $data['input_selected_redemption_ids'];
        if(!$input_selected_redemption_ids ){
            return true;
        }
        $my_redemption_ids = [];
        foreach ($redemption_list as $item){
            $my_redemption_ids[] = $item['id'];
        }
        foreach ($input_selected_redemption_ids as $id){
            if(!in_array($id,$my_redemption_ids)){//输入的id，不在我的兑换列表里面
                return false;
            }
        }
        return true;
    }

}