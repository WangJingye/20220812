<?php


namespace App\Http\Controllers\Outward\Goods;

use App\Service\Goods\ProductService;
use Illuminate\Http\Request;
use App\Service\Goods\StockService;
use App\Http\Controllers\Api\Controller;
use Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SkuStockController extends Controller
{
    /**
     * @api {get} get/stock
     * @apiName
     * @apiGroup Stock
     * @apiDescription
     * @apiParam {String}   sku
     * @apiParam {String}   channel_id 渠道id
     */
    public function getStock(Request $request)
    {
        $v = Validator::make($request->all(), [
            'sku' => 'required',
            'channel_id' => 'required',
        ]);

        if ($v->fails()) {
            return $this->error(0,$v->errors()->first());

        }
        list($code,$message,$data) = StockService::getStockOne($request->sku, $request->channel_id);
        if ($code) {
            return $this->success($data);
        }

        return $this->error($code,$message);
    }

    /**
     * @api {get} get/batchStock
     * @apiName
     * @apiGroup Stock
     *
     * @apiDescription
     * @apiParam {String}   {}
     * @apiParam {String}   channel_id 渠道id
     */
    public function getBatchStock(Request $request)
    {
        $v = Validator::make($request->all(), [
            'sku_ids' => 'required',
            'channel_id' => 'required',
        ]);

        if ($v->fails()) {
            return $this->error(0,$v->errors()->first());

        }

        $ids = explode(',',$request->sku_ids);
        if(!$ids){
            return $this->error('sku_ids解析出错，请确认是否是逗号分隔');
        }
        list($code,$message,$data) = StockService::getBatchStock($ids, $request->channel_id);
        if ($code) {
            return $this->success($data);
        }

        return $this->error($code,$message);
    }

    /**
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
        $v = Validator::make($request->all(), [
            'sku_json' => 'required',
            'channel_id' => 'required',
            'increment' => 'required|numeric',
            'no_lock' => 'nullable|numeric',
        ]);
        if ($v->fails()) {
            return $this->error(0,$v->errors()->first());
        }

        $sku_array = json_decode($request->sku_json,true);
        if(!$sku_array){
            return $this->error(0,'sku_json解析出错，请确认格式');
        }
        $no_lock = $request->get('no_lock')?:0;
        list($code,$message,$data) = StockService::batchUpdateStock($sku_array,$request->increment, $request->channel_id,$no_lock);
        if ($code) {
            return $this->success($data);
        }
        return json_encode(['code'=>0,'message'=>'fail','data'=>$data]);
    }

    /**
     * 后台手动更新库存接口
     * @param Request $request
     * @return array|false|string
     */
    public function updateBatchStockForce(Request $request)
    {
        $v = Validator::make($request->all(), [
            'sku_json' => 'required',
            'channel_id' => 'required',
            'increment' => 'required|numeric',
        ]);
        if ($v->fails()) {
            return $this->error(0,$v->errors()->first());
        }

        $sku_array = json_decode($request->sku_json,true);
        if(!$sku_array){
            return $this->error(0,'sku_json解析出错，请确认格式');
        }

        $note = $request->get('note');
        list($code,$message,$data) = StockService::batchUpdateStockForce($sku_array,$request->increment, $request->channel_id,$note);
        if ($code) {
            return $this->success($data);
        }
        return json_encode(['code'=>0,'message'=>'fail','data'=>$data]);
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
        ]);

        if ($v->fails()) {
            return $this->error(0,$v->errors()->first());
        }

        $status = StockService::unlockSku($request->sku,$request->channel_id,$request->num);
        if ($status) {
            return $this->success([]);
        }

        return $this->error(0);
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
        $sku_array = json_decode($request->sku_json,true);
        if(!$sku_array){
            return $this->error(0,'sku_json解析出错，请确认格式');
        }
        $status = StockService::batchUnlockSku($sku_array,$request->channel_id);
        if ($status) {
            return $this->success([]);
        }

        return $this->error(0);
    }

    public function updateBatchStockFull(Request $request)
    {
        $v = Validator::make($request->all(), [
            'sku_json' => 'required',
            'force' => 'nullable',
        ]);
        if ($v->fails()) {
            return $this->error(0,$v->errors()->first());
        }
        //{"skutest001":1}
        $skus = $request->get('sku_json');
        $force = $request->get('force')?1:0;
        $result = StockService::updateStockFull($skus,$restore_skus,$force);
        if($result===true){
            return $this->success(compact('restore_skus'));
        }
        return json_encode(['code'=>0,'message'=>$result]);
    }

    public function updateBatchPrice(Request $request)
    {
        $v = Validator::make($request->all(), [
            'sku_json' => 'required',
        ]);
        if ($v->fails()) {
            return $this->error(0,$v->errors()->first());
        }
        $skus = $request->get('sku_json');
        if(ProductService::updateBatchPrice($skus)){
            return $this->success([]);
        }return json_encode(['code'=>0,'message'=>'failed']);
    }
}