<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/6/1
 * Time: 16:26
 */

namespace App\Http\Controllers\Api\Oms;

use App\Http\Controllers\Api\ApiController;
use App\Lib\GuzzleHttp;
use Illuminate\Http\Request;

class HandleSkuController extends ApiController
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
    public function updateBatchStock(Request $request)
    {
        $params = $request->all();
        $url = config('api.map')['update/batchStock'];

        $http_params = [
            'method' => 'post',
            'data' => $params,
            'type' => 'FORM',
            'url' => $url
        ];
        $content = GuzzleHttp::httpRequest($http_params);
        $result = json_decode($content,true);
        if(!$result || !is_array($result)){
            return $this->error('请求结果异常');
        }
        return $result;

    }

    /**
     * 支付成功后解除锁定库存
     * @api {post} update/unlockSku
     * @apiName
     * @apiGroup Stock
     *
     * @apiDescription
     * @apiParam {String}   {}
     * @apiParam {String}   channel_id 渠道id
     * 1增 0减
     */
    public function unlockSku(Request $request)
    {

        $v = Validator::make($request->all(), [
            'sku' => 'required',
            'channel_id' => 'required',
            'num' => 'required|numeric|min:1',
            'increment' => 'required|numeric',
        ]);

        if ($v->fails()) {
            return $this->error(0,$v->errors()->first());

        }

        list($code,$message) = StockService::updateStock($request->sku,$request->increment,$request->num,$request->channel_id);
        if ($code) {
            return $this->success([]);
        }

        return $this->error(0,$message);
    }

    /**
     * 支付成功后批量解除锁定库存
     * @api {post} update/batchUnlockSku
     * @apiName
     * @apiGroup Stock
     * 1增 0减
     */
    public function batchUnlockSku(Request $request)
    {

        $v = Validator::make($request->all(), [
            'sku_json' => 'required',
            'channel_id' => 'required',
        ]);

        if ($v->fails()) {
            return $this->error(0,$v->errors()->first());
        }

        list($code,$message) = StockService::updateStock($request->sku,$request->increment,$request->num,$request->channel_id);
        if ($code) {
            return $this->success([]);
        }

        return $this->error(0,$message);
    }
}
