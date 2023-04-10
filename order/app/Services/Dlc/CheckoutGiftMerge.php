<?php namespace App\Services\Dlc;

class CheckoutGiftMerge
{
    public static function make($gifts){
        try{
            if(!empty($gifts) && is_array($gifts)){
                $new_gifts = [];
                foreach($gifts as $gift){
                    if(array_key_exists($gift['sku'],$new_gifts)){
                        $new_gifts[$gift['sku']]['qty'] += $gift['qty'];
                    }else{
                        $new_gifts[$gift['sku']] = $gift;
                    }
                }
                $new_gifts = array_values($new_gifts);
            }
            return $new_gifts??$gifts;
        }catch (\Exception $e){
            return $gifts;
        }
    }














}
