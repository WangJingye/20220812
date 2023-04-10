<?php namespace App\Service\Dlc;

class Rule
{
    /**
     * 根据促销规则填充描述名称
     * @param $rules
     */
    public static function fill(&$rules){
        foreach($rules as &$rule){
            $rule['extend_name'] = call_user_func(function() use($rule){
                $extend_name = '';
                switch ($rule['type']){
                    case 'product_discount'://直接折扣
                    case 'code_product_discount'://直接折扣优惠码
                        $product_discount = $rule['product_discount'];
                        $extend_name = "折扣{$product_discount}%";
                        break;
                    case 'n_piece_n_discount'://多件多折
                    case 'code_n_piece_n_discount'://多件多折优惠码
                        $nn_n = explode(',',$rule['nn_n']);
                        $nn_discount = explode(',',$rule['nn_discount']);
                        foreach(array_combine($nn_n,$nn_discount) as $k=>$v){
                            $extend_name .= ("满{$k}件折扣{$v}%,");
                        }
                        $extend_name = rtrim($extend_name,',');
                        break;
                    case 'full_reduction_of_order':
                    case 'coupon':
                    case 'code_full_reduction_of_order'://满减优惠码
                        $total_amount = explode(',',$rule['total_amount']);
                        $total_discount = explode(',',$rule['total_discount']);
                        foreach(array_combine($total_amount,$total_discount) as $k=>$v){
                            $extend_name .= ("满{$k}元减{$v}元,");
                        }
                        $extend_name = rtrim($extend_name,',');
                        break;
                    case 'order_n_discount':
                    case 'code_order_n_discount'://每满减优惠码
                        $step_amount = explode(',',$rule['step_amount']);
                        $step_discount = explode(',',$rule['step_discount']);
                        foreach(array_combine($step_amount,$step_discount) as $k=>$v){
                            $extend_name .= ("每满{$k}元减{$v}元,");
                        }
                        $extend_name = rtrim($extend_name,',');
                        break;
                    case 'gift':
                    case 'free_try':
                    case 'code_gift'://赠品-优惠码
                        $gift_amount = $rule['gift_amount'];
                        $gift_n = $rule['gift_n'];
                        $gwp_skus = $rule['gwp_skus'];
                        $rand = $rule['is_rand']?'随机':'';
                        $condition = $gift_amount?("{$gift_amount}元"):"{$gift_n}件";
                        $extend_name = "满{$condition}{$rand}送赠品{$gwp_skus}";
                        $is_step = array_get($rule,'is_step')?1:0;
                        if($is_step){
                            $extend_name = '每'.$extend_name;
                        }
                        break;
                }
                return $extend_name;
            });
        }
    }

    /**
     * 计入步长
     * @param $rule
     * @param $total_amount
     * @param $total_qty
     */
    public static function makeStep(&$rule,$total_amount,$total_qty){
        if(in_array($rule['type'],['gift','code_gift']) && array_get($rule,'is_step')==1){
            $times = call_user_func(function() use($rule,$total_amount,$total_qty){
                $rule_total_amount = $rule['gift_amount'];
                if($rule_total_amount > 0 and $total_amount >= $rule_total_amount){
                    return floor($total_amount/$rule_total_amount);
                }
                $gift_n = $rule['gift_n'];
                if($gift_n > 0 and $total_qty >= $gift_n){
                    return floor($total_qty/$gift_n);
                }
                return 0;
            });
            $gwp_skus = '';
            for($i=0;$i<$times;$i++){
                $gwp_skus .= $rule['gwp_skus'].',';
            }
            $rule['gwp_skus'] = rtrim($gwp_skus,',');
        }
    }

    public static function randSelect(&$rule,$gift_stock){
        if(in_array($rule['type'],['gift']) && array_get($rule,'is_rand')==1){
            if($rule['gwp_skus']){
                $gwp_skus_arr = explode(',',$rule['gwp_skus']);
                $gwp_skus_arr_count_values = array_count_values($gwp_skus_arr);
                $gifts = [];
                foreach($gwp_skus_arr_count_values as $sku=>$qty){
                    if(array_key_exists($sku,$gift_stock)){
                        if($gift_stock[$sku]>=$qty){
                            $gifts[] = [
                                'sku'=>$sku,
                                'qty'=>$qty,
                            ];
                        }
                    }
                }

                if($gifts){
                    $gift = array_random($gifts);
                    $gwp_skus = '';
                    for($i=0;$i<$gift['qty'];$i++){
                        $gwp_skus .= $gift['sku'].',';
                    }
                    $gwp_skus = rtrim($gwp_skus,',');
                }
                $rule['gwp_skus'] = $gwp_skus??'';
            }
        }
    }

}