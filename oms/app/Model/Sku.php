<?php

namespace App\Model;

use Illuminate\Support\Facades\Log;
use App\Lib\GuzzleHttp;

class Sku
{
    /**
     * 批量更新库存
     * @api {post} update/batchStock
     * @apiName
     * @apiGroup Stock
     *
     * @apiDescription
     * @apiParam {String}   {}
     * @apiParam {String}   channel_id 渠道id
     * 1增 0减
     */
    public function updateBatchStock($sku_json,$channel_id,$increment,$no_lock=0)
    {

        $from_params = [
            'sku_json'=>$sku_json,
            'channel_id'=>$channel_id,
            'increment'=>$increment,
        ];
        if($no_lock==1){
            $from_params['no_lock'] = 1;
        }
        $url = config('api.map')['update/batchStock'];
        $http_params = [
            'method' => 'post',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params);
        Log::info('updateBatchStock:request',$http_params);
        Log::info('updateBatchStock:reponse:'.$content);
        $result = json_decode($content,true);
        if(!$result || !is_array($result)){
            return false;
        }
        return $result;

    }

    /**
     * 批量更新库存
     * @api {post} update/batchStock
     * @apiName
     * @apiGroup Stock
     *
     * @apiDescription
     * @apiParam {String}   {}
     * @apiParam {String}   channel_id 渠道id
     * 1增 0减
     */
    public function batchUnlockSku($sku_str,$channel_id)
    {

        $from_params = [
            'sku_json'=>$sku_str,
            'channel_id'=>$channel_id,
        ];
        $url = config('api.map')['batch/unLockStock'];
        $http_params = [
            'method' => 'post',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params);
        Log::info('batchUnlockSku:request',$http_params);
        Log::info('batchUnlockSku:reponse:'.$content);
        $result = json_decode($content,true);
        if(!$result || !is_array($result)){
            return false;
        }
        return $result;

    }

    public function getSku($sku_ids)
    {

        $from_params = [
            'sku_ids'=>$sku_ids,
            'simple'=>1
        ];
        $url = config('api.map')['outward/product/getProductInfoBySkuIds'];
        $http_params = [
            'method' => 'get',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params);
        Log::info('getSku:request',$http_params);
        Log::info('getSku:reponse:'.$content);
        $result = json_decode($content,true);
        if(!$result || !is_array($result)){
            return false;
        }
        return $result;

    }

    public function addSkuStock($data)
    {
        $from_params= [
            'json_str'=>json_encode($data)
        ];
        $url = config('api.map')['goods/insertStock'];
        $http_params = [
            'method' => 'post',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params);
        Log::info('addSkuStock:request',$http_params);
        Log::info('addSkuStock:reponse:'.$content,[]);
        $result = json_decode($content,true);
        if(!$result || !is_array($result)){
            return false;
        }
        return $result['code'];

    }

    public function getUserLevel($pos_id){
        $from_params= [
            'pos_id'=>$pos_id
        ];
        $url = config('api.map')['member/getUserInfo'];
        $http_params = [
            'method' => 'post',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params);
        Log::info('getUserLevel:request',$http_params);
        Log::info('getUserLevel:reponse:'.$content,[]);
        $result = json_decode($content,true);
        if(!$result || !is_array($result)){
            return false;
        }
        return $result['data'];
    }



}
