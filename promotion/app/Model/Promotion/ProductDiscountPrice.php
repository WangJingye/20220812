<?php

namespace App\Model\Promotion;


use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use Illuminate\Support\Facades\DB;
use App\Model\Promotion\Cart;
use App\Lib\Http;
//跟商品交互的接口逻辑
class ProductDiscountPrice  {
    
    //调用商品接口,告诉商品创建分类
    public function invokeProductApi($rule){
        $http = new Http();
        $data = [
            'cid'=>$rule['cids'],
            'exclude'=>strtoupper($rule['exclude_sku']),
            'extra'=>strtoupper($rule['add_sku']),
            'rule_id'=>$rule['id'],
        ];
        return $http->curl('goods/rule/category',$data);
    }
    //推送到延迟队列
    public function pushToDelayMq($rule_id){
        $rule = new Cart();
        $rule = $rule->where('id',$rule_id)->get()->toArray();
        $rule = $rule[0];
        return $this->invokeProductApi($rule);
    }
    //商品是否有直接折扣促销，只返回一个
    private function _productList($rules,$item){
        foreach($rules as $rule){
            $cids = explode(',',$rule['cids']);
            $item_cids = explode(',',$item['cid']);
            $excludes_style_number = explode(',',strtoupper($rule['exclude_sku']));
            $includes_style_number = explode(',',strtoupper($rule['add_sku']));
            if(in_array(strtoupper($item['styleNumber']), $excludes_style_number)){
                continue;
            }
            if(!array_intersect($item_cids,$cids) and !in_array(strtoupper($item['styleNumber']),$includes_style_number)){
                continue;
            }
            return $rule;
        }
        return false;
    }
    //商品列表页面，促销信息，只显示直接折扣信息
    //[{"model_id":"","cid":"xx","styleNumber":"xx"}]
    public function productList($data){
        $curr_time = date('Y-m-d H:i:s');
        $where = [
            ['status','=',2,],['start_time','<',$curr_time],['end_time','>',$curr_time],//激活，有效的
        ];
        $whereIn = ['product_discount','n_piece_n_discount'];
        $rules = Cart::where($where)->whereIn('type',$whereIn)
                    ->get()->toArray();
        $new_item = [];
        foreach($data as $item){
            $rule = $this->_productList($rules, $item);
            $item['discount'] = '';
            $item['rule_name'] = '';
//             $item['display_name'] = '';
            $item['type'] = '';
            if($rule){
                $item['discount'] = $rule['product_discount'];
                $item['rule_name'] = $rule['display_name'];
//                 $item['display_name'] = $rule['display_name'];
                $item['type'] = $rule['type'];
            }
            $new_item[] = $item;
        }
        return $new_item;
    }
    //商品详情页面，促销信息，显示所有的促销信息
    //[{"model_id":"","cid":"xx","styleNumber":"xx"}]
    public function productDetail($data_arr){
        $curr_time = date('Y-m-d H:i:s');
        $where = [
            ['status','=',2,],['start_time','<',$curr_time],['end_time','>',$curr_time],//激活，有效的
        ];
        $rules = Cart::where($where)
                    ->get()->toArray();
        $db_rules = $rules;
        $return = [];
        foreach($data_arr as $data){
            $rules = $this->_productDetail($db_rules, $data);
            $new_items = [];
            foreach($rules as $rule){
                $new_items[] = [
                    'type'=>$rule['type'],
                    'rule_name'=>$rule['name'],
                    'discount'=>$rule['product_discount'],
                    'rule_id'=>$rule['id'],
                ];
            }
            $sorted_items = $this->_sortRules($new_items);
            $data['rule'] = $sorted_items;
            $return[] = $data;
        }
        return $return;
    }
    //按照指定顺序返回
    private function _sortRules($rules){
        $allow_promotion_type = [
            'product_discount'=>'直接折扣',
            'n_piece_n_discount'=>'多件多折',
            'full_reduction_of_order'=>'满减',
            'order_n_discount'=>'每满减',
            'gift'=>'赠品',
        ];
        $new_items = [];
        foreach($allow_promotion_type as $allow_type_key=>$allow_type_name){
            foreach($rules as $rule){
                if($rule['type'] == $allow_type_key){
                    $new_items[] = $rule;
                }
            }
        }
        return $new_items;
    }
    private function _productDetail($rules,$item){
        $new_item = [];
        foreach($rules as $rule){
            $cids = explode(',',$rule['cids']);
            $item_cids = explode(',',$item['cid']);
            $excludes_style_number = explode(',',strtoupper($rule['exclude_sku']));
            $includes_style_number = explode(',',strtoupper($rule['add_sku']));
            if(in_array(strtoupper($item['styleNumber']), $excludes_style_number)){
                continue;
            }
            if(!array_intersect($item_cids,$cids) and !in_array(strtoupper($item['styleNumber']),$includes_style_number)){
                continue;
            }
            $new_item[] = $rule;
        }
        return $new_item;
    }
}
