<?php
namespace App\Services\Checkout\Data;

//赠品显示，遍历所有的赠品rule, 看有哪些主商品。如果主商品里面有多个rule，合并显示
//把赠品和主商品分好组
class Gift
{
    public $data = [];
    public $promotion_data = [];

    public function init($data){
        $this->data = $data;
        $this->promotion_data = $this->data['promotion_data'];
        return $this;
    }
    //遍历每个赠品rule，属于哪些主商品
    public function loopGiftRule(){
        if(!isset($this->promotion_data['order_gift'])){
            return $this->data;
        }
        $this->groupWholeGifts();
        $group = $this->groupMainGift();
        $this->data['goods_list']['goods'] = $group;
//        $this->data['goods_list_backup']['goods'] = $group;
        return $this->data;
    }
    //把全场的赠品放到外面，全部商品都应用了相同的赠品rule_id的，赠品放到外面
    public function groupWholeGifts(){
        $cart_items = $this->promotion_data['cartItems'];
        $count_items = count($cart_items);
        $whole_rule_array = [];
        //找出main item 的applied_rule_ids里面的rule_id应用了多少次
        foreach ($cart_items as $item){
            $applied_rule_ids = $item['applied_rule_ids']??[];
            if(!$applied_rule_ids){//如果有一个主商品没有应用促销，就没有全场赠品了
                return;
            }
            $item_rule_ids = [];
            foreach ($applied_rule_ids as $_rule_id){
                $sub_type = $_rule_id['sub_type']??'';
                if(($_rule_id['type'] == 'gift' or $sub_type == 'code_gift') and $_rule_id['is_special_gift'] != '1'){
                    $item_rule_ids[] = $_rule_id['rule_id'];
                }
            }
            $item_rule_ids = array_unique($item_rule_ids);//一个main_item，不允许应用两个相同的rule_id
            foreach ($item_rule_ids as $_rule_id_item){
                $whole_rule_array[$_rule_id_item] = $whole_rule_array[$_rule_id_item]??0;
                $whole_rule_array[$_rule_id_item] = $whole_rule_array[$_rule_id_item] + 1;
            }
        }
        //找到一个赠品rule_id出现在所有的main item里面的rule_id
        $fit_rule_id_arr = [];
        foreach ($whole_rule_array as $rule_id=>$count){
            if($count == $count_items){//出现的数量跟main item的数量一样，说明每个main item都有
                $fit_rule_id_arr[] = $rule_id;
            }
        }
        //把这些符合的rule_id在cart_item的applied_rule_ids里面拿走
        $new_cart_items = [];
        foreach ($cart_items as $item){
            $applied_rule_ids = $item['applied_rule_ids']??[];
            foreach ($applied_rule_ids as $key=>$_rule){
                $rule_id = $_rule['rule_id'];
                $sub_type = $_rule['sub_type']??'';
                if(($_rule['type'] != 'gift' and $sub_type != 'code_gift' ) or $_rule['is_special_gift'] == '1'){
                    continue;
                }
                if(in_array($rule_id,$fit_rule_id_arr)){
                    unset($applied_rule_ids[$key]);
                }
            }
            if(!is_array($applied_rule_ids)){
                $applied_rule_ids = [];
            }
            $item['applied_rule_ids'] = $applied_rule_ids;
            $new_cart_items[] = $item;
        }
        $this->promotion_data['cartItems'] = $new_cart_items;
        //符合条件的赠品
        $fit_gift_skus = [];
        foreach($fit_rule_id_arr as $rule_id){
            $fit_gift_skus = $this->getGiftSkusByRuleId($rule_id,$fit_gift_skus);
        }
        //组装
        $fit_gift_skus_final = [];
        foreach($fit_gift_skus as $sku){
            $fit_gift_skus_final[] = ['sku'=>$sku,'qty'=>1];
        }
        $this->data['whole_gifts_skus'] = $fit_gift_skus_final;//全场赠品skus
        //全场赠品，根据促销规则再次分组--------------------------------
        $fit_gift_skus = [];
        foreach($fit_rule_id_arr as $rule_id){
            $rule_name = $this->getGiftRuleNameByRuleId($rule_id);
            $skus = $this->getGiftSkusByRuleId($rule_id,[]);
            $fit_gift_skus[$rule_id] = ['rule_name'=>$rule_name,'rule_id'=>$rule_id,'skus'=>$skus];
        }
        //组装
        $fit_gift_skus_final = [];
        foreach($fit_gift_skus as $item){
            $fit_gift_skus_final = [];
            $rule_name = $item['rule_name'];
            foreach($item['skus'] as $_sku){
                $fit_gift_skus_final[] = ['sku'=>$_sku,'qty'=>1];
            }
            $this->data['another_whole_gifts_skus'][] = ['rule_name'=>$rule_name,
                                                            'gift_skus'=>$fit_gift_skus_final];
        }
    }
    //主商品，赠品分组
    //有相同gift_rule_id的都放在一起
    private function groupMainGift(){
        $cart_items = $this->promotion_data['cartItems'];
        $group = [];
        $main_items = [];
        $gift_items = [];
        //先把没有应用规则和没有应用赠品规则的都排除
        foreach($cart_items as $key=>$item){
            $applied_rule_ids = $item['applied_rule_ids']??[];
            if(!$applied_rule_ids){//没有应用规则
                $main_items[] = $item;
                $group[] = [
                    'main'=>$main_items,
                    'gifts'=>$gift_items,
                ];
                $main_items = [];
                $gift_items = [];
                unset($cart_items[$key]);
                continue;
            }
            $flag_has_gift_rule = false;
            foreach($applied_rule_ids as $applied_rule){
                $sub_type = $applied_rule['sub_type']??'';
                if($applied_rule['type'] == 'gift' or $sub_type == 'code_gift'){
                    $flag_has_gift_rule = true;
                }
            }
            if(!$flag_has_gift_rule){//没有应用赠品规则
                $main_items[] = $item;
                $group[] = [
                    'main'=>$main_items,
                    'gifts'=>$gift_items,
                ];
                $main_items = [];
                $gift_items = [];
                unset($cart_items[$key]);
            }
        }
        if(count($cart_items) < 1){
            return $group;
        }
        //剩余的cart_items都是有应用赠品规则的,有相同gift_rule_id的都放在一起
        //先拿第一个商品，然后拿剩余的（相同gift rule的），然后再循环第一个开始
        //递归循环
loopAgain:
//        $this->data['debug_cart_items'][] = $cart_items;
//        $this->data['debug_group'][] = $group;
        $return = $this->getSameRuleItems($cart_items);
        $cart_items = $return['cart_items'];
        $group[] = $return['group'];
        if(count($cart_items)){
            goto loopAgain;
        }
        return $group;
    }

    private function getArrayFirstItem($cart_items){
        foreach ($cart_items as $key=>$item){
            return ['key'=>$key,'item'=>$item];
        }
    }

    //先拿第一个商品，然后拿剩余的（相同gift rule的），然后再循环第一个开始
    private function getSameRuleItems($cart_items){
        $main_items = [];
        $gift_items = [];
        $same_rule = [];//具有相同的gift rule都放在一起
//        $one_item = array_pop($cart_items);
        $one_item_array = $this->getArrayFirstItem($cart_items);
        $one_item = $one_item_array['item'];
        $one_item_key = $one_item_array['key'];
        $cart_items[$one_item_key] = false;
        unset($cart_items[$one_item_key]);
        $applied_rule_ids = $one_item['applied_rule_ids'];
        $main_items[] = $one_item;
        foreach($applied_rule_ids as $applied_rule){
            $sub_type = $applied_rule['sub_type']??'';
            if($applied_rule['type'] == 'gift' or $sub_type =='code_gift' ){
                $rule_id = $applied_rule['rule_id'];
//                $gift_items = $this->getGiftSkusByRuleId($rule_id,$gift_items);
                $same_rule[] = $rule_id;
            }
        }
//        $this->data['debug_same_rule_items'][] = $cart_items;
//        $this->data['debug_same_rule_main_items'][] = $main_items;
        foreach($cart_items as $key=>$item){
            if($item == false){
                continue;
            }
            $applied_rule_ids = $item['applied_rule_ids'];
            foreach($applied_rule_ids as $applied_rule){
                $sub_type = $applied_rule['sub_type']??'';
                if($applied_rule['type'] == 'gift' or $sub_type =='code_gift'){
                    $rule_id = $applied_rule['rule_id'];
                    if(in_array($rule_id,$same_rule)){//要跟第一个商品有相同的gift rule
                        $main_items[] = $item;
//                        $gift_items = $this->getGiftSkusByRuleId($rule_id,$gift_items);
                          $gift_items_return = $this->getMainItemGiftSkus($item,$gift_items,$same_rule);
                        $same_rule = $gift_items_return['same_rule'];
//                        $gift_items = $gift_items_return['gift_items'];
                        unset($cart_items[$key]);//拿掉一个
                        break;
                    }
                }
            }
        }
        //根据main_item的所有的rule_id来获取所有的赠品skus
        $same_rule = array_unique($same_rule);
        foreach ($same_rule as $rule_id){
            $gift_items = $this->getGiftSkusByRuleId($rule_id,$gift_items);
        }
        $gift_rule_name_arr = [];
        foreach($same_rule as $rule_id){
            $gift_rule_name_arr[] = $this->getGiftRuleNameByRuleId($rule_id);
        }
        $gift_rule_name_str = implode(',',$gift_rule_name_arr);
//        $this->data['debug_same_rule_main_items'][] = $main_items;
        //格式化赠品
        $new_gift = [];
//        $gift_items = array_unique($gift_items);
        foreach($gift_items as $item){
            $new_gift[] = ['sku'=>$item,'qty'=>1];
        }
        $group = [
            'main'=>$main_items,
            'gifts'=>$new_gift,
            'gifts_rule_name'=>$gift_rule_name_str,
        ];
        return [
            'cart_items'=>$cart_items,
            'group'=>$group,
        ];
    }

    //根据赠品规则获取规则名称
    private function getGiftRuleNameByRuleId($rule_id){
        $gifts = $this->promotion_data['order_gift'];
        foreach($gifts as $item){
            if($item['rule_id'] == $rule_id){
                return $item['rule_name'];
            }
        }
    }

    //获取这个item的所有gift skus
    private function getMainItemGiftSkus($main_item,$gift_items=[],$same_rule=[]){
        $applied_rule_ids = $main_item['applied_rule_ids'];
        foreach($applied_rule_ids as $applied_rule){
            $sub_type = $applied_rule['sub_type']??'';
            if($applied_rule['type'] == 'gift' or $sub_type == 'code_gift'){
                $same_rule[] = $applied_rule['rule_id'];
                $gifts = explode(',',$applied_rule['gift_skus']);
                foreach($gifts as $g){
                    $gift_items[] = $g;
                }
            }
        }
        return ['same_rule'=>$same_rule,'gift_items'=>$gift_items];
    }

    private function getGiftSkusByRuleId($rule_id,$gift_items=[]){
        $gifts = $this->promotion_data['order_gift'];
        foreach($gifts as $item){
            if($item['rule_id'] == $rule_id){
                $gifts = explode(',',$item['gift_skus']);
                foreach($gifts as $g){
                    $gift_items[] = $g;
                }
                return $gift_items;
            }
        }
    }
}
