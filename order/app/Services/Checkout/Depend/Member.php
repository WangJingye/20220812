<?php
namespace App\Services\Checkout\Depend;

class Member
{
    public $address_api = 'coupon/getMemberInfo';
    public $member_info_sample_data = [
        'customer_id'=>'',
        'user_id'=>'',
        'pos_id'=>'',
        'shipping_address'=>[
            [
                'shipping_address_id'=>'',
                'name'=>'name',//收货人
                'sex'=>'man',//性别
                'mobile'=>'135',
                'province'=>'上海',//省市区
                'city'=>'上海',
                'district'=>'徐汇',
                'address_detail'=>'平福路188号',//详细地址
                'post_code'=>'200090',//邮编
                'selected'=>1,
            ]
        ],
        'coupon_list'=>[
            ['coupon_id'=>'1','name'=>'1'],
            ['coupon_id'=>'2','name'=>'2']
        ],
        'total_points'=>'100',//顾客拥有的积分
    ];
    //获取积分和配送地址信息，还有用户优惠券列表
    //如果获取不到配送地址，返回错误
    public function getMemberInfo($data_obj){
        /** @var \App\Services\Checkout\Data\Data $data_obj */
        $data = $data_obj->getData();
        $customer_id = $data['customer_id'];
        $shipping_address_id = $data['shipping_address_id'];
        $api = app('ApiRequestInner',['module'=>'member']);
        $input_data = ['shipping_address_id'=>$shipping_address_id,'uid'=>$customer_id];
        $resp = $api->request($this->address_api,'POST',$input_data);
        $resp = $resp['data'];
        $resp['user_id'] = $customer_id;
        $member_info = $this->parseMember($resp,$shipping_address_id);
        if($shipping_address_id and $member_info == false ){//输入的shipping_address_id在我的地址簿里面找不到
            return false;
        }
        $data_obj->setMemberInfo($member_info)->setUsedPoints($member_info)->setShippingAddress();
        return $data_obj;
    }

    //解析会员接口返回的数据
    private function parseMember($member_data,$shipping_address_id){
        $member_data['customer_id'] = $member_data['user_id'];
        $shipping_address_arr = $member_data['shipping_address']??[];
        $new_item = [];
        $exist_flag = 0;
        foreach($shipping_address_arr as $item){
            $item['selected'] = 0;
            if($item['shipping_address_id'] == $shipping_address_id){
                $item['selected'] = 1;
                $exist_flag = 1;
            }
            $new_item[] = $item;
        }
        $member_data['shipping_address'] = $new_item;
        if($shipping_address_id and $exist_flag == 0){
            return false;
        }
        return $member_data;
    }

    //扣除积分，如果失败需要归还的
    public function decreasePoints($data_obj){
        return $data_obj;
    }

    //归还积分，如果3次都不成功就放到失败队列，通过脚本运行归还
    public function restorePoints($points){

        return true;
    }
}
