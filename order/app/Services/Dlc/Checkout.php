<?php namespace App\Services\Dlc;

class Checkout
{
    /**
     * 如果优惠码无效则coupon_code为空 并且将所有使用中的促销放到whole_order_gift中包括实物券,用于保存订单到oms
     * @param $data
     */
    public static function filter(&$data):void{
        if(empty($data['promotion_data']['code_applied'])){
            $data['coupon_code'] = '';
        }
        $data['whole_order_gift'] = array_reduce(array_get($data,'goods_list.goods'),function($list,$goods){
            foreach($goods['main'] as $item){
                foreach(array_get($item,'applied_rule_ids') as $rule){
                    $list[] = $rule;
                }
            }
            return $list;
        },[]);
        if(!empty($data['promotion_data']['product_coupon_sku'])){
            $product_coupon_sku = array_reduce($data['promotion_data']['product_coupon_sku'],function($result,$item){
                $item['extend_name'] = '实物SKU:'.$item['product_coupon_sku'];
                $item['type'] = 'product_coupon';
                $result[] = $item;
                return $result;
            });
            $data['whole_order_gift'] = array_merge($data['whole_order_gift'],$product_coupon_sku);
        }
    }

    public static function addRuleInfo(&$goods){
        try{
            if($goods){
                foreach($goods as &$good){
                    foreach($good['main'] as &$item){
                        if(!empty($item['applied_rule_ids'])){
                            $rule_names = array_reduce($item['applied_rule_ids'],function($result,$item){
                                if(!(isset($item['is_special_gift']) && $item['is_special_gift']===0)){
                                    $result[] = $item['rule_name'];
                                }return $result;
                            },[]);
                        }
                        $item['promotion_names'] = empty($rule_names)?'':implode(',',$rule_names);
                    }
                }
            }
        }catch (\Exception $e){
            return ;
        }
    }

    /**
     * 将地址ID转换为省
     * @param $data_obj
     * @return \App\Services\Checkout\Data\Data
     */
    public static function convertAddress2Province($data_obj){
        $curr_address_id = request()->get('shipping_address_id');
        /** @var \App\Services\Checkout\Data\Data $data_obj */
        $data = $data_obj->getData();
        $shipping_address = array_get($data,'member_info.shipping_address');
        $province = $default_province = [];
        foreach($shipping_address as $addr){
            $province[$addr['shipping_address_id']] = $addr['province'];
            if($addr['is_default']==1){
                $default_province = $addr['province'];
            }
        }
        if($curr_address_id){
            $curr_province = array_get($province,$curr_address_id);
        }else{
            $curr_province = $default_province;
        }
        if(!empty($curr_province)){
            $data_obj->setAddress($curr_province);
        }
        return $data_obj;
    }
}
