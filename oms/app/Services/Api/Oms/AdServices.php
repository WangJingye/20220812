<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/18
 * Time: 17:20
 */

namespace App\Services\Api\Oms;


use App\Model\Help;

class AdServices
{
    const M_ID = 'dlc';   //广告主ID  领克特提供
    //推送订单到领克特
    public static function getPushOrderToLinktechLink($order){
        try{
            $LTINFO = request()->ltinfo??'';

            Help::Log('领克特cookie:',$LTINFO);

            if(!$LTINFO) return '';
            Help::Log('领克特订单信息:',$order);
//        logger("待支付订单信息：".json_encode($order));

            $products = $order['sub_orders'];
            $user_id = $order['user_id']??0;
            $pids = array_column($products,'sku_id');
            $order_id = $order['order_num'];
            $p_nums = array_column($products,'buy_num');
            $p_prices = array_column($products,'price');

            if( $LTINFO && $order_id && $pids && $order_id && $p_nums && $p_prices ){
                $lt_o_cd = $order_id;
                $lt_p_cd = implode('||',$pids);
                $lt_price = implode('||',$p_prices);
                $lt_it_cnt = implode('||',$p_nums);
//                $lt_c_cd = implode('||',$p_prices);
                $lt_c_cd = '';
//            $lt_p_cd = substr($lt_p_cd, 2);
//            $lt_price = substr($lt_price, 2);
//            $lt_it_cnt = substr($lt_it_cnt, 2);
//            $lt_c_cd = substr($lt_c_cd, 2);

                $merchant_id 	= self::M_ID;
                $lt_mbr_id 	= $user_id;

                $url = "http://service.linktech.cn/purchase_cps.php?a_id=$LTINFO".
                    "&m_id={$merchant_id}&mbr_id={$lt_mbr_id}&o_cd={$lt_o_cd}&p_cd={$lt_p_cd}".
                    "&price={$lt_price}&it_cnt={$lt_it_cnt}&c_cd=$lt_c_cd";
//            logger("待支付订单推送：{$url}");
                return $url;
//                echo $url;
//
//                $http_params = [
//                    'method' => 'get',
//                'data' => [],
//                'type' => '',
//                    'url' => $url
//                ];
//
//                $ret = GuzzleHttp::httpRequest($http_params,['verify'=>false]);
//                dd($ret);


            }
            return '';
        }catch(\Exception $e){
            return '';
        }

    }
}
