<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use App\Model\Goods\Spec;
use App\Model\Goods\Spu;
use App\Service\Goods\ProductService;
use App\Service\Goods\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Model\Goods\Sku;
use App\Model\Goods\StockLog;
use App\Model\Goods\ProductHelp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SkuController extends Controller
{
    protected $_model = Spu::class;

    /**
     * SKU列表，支持模糊查询.
     */
    public function list(Request $request)
    {

        $limit = $request->limit ?: 100;
        $query = new Sku();
        $pService = new ProductService();
        if ($request->sku_id) {
            $query = $query->where('sku_id', $request->sku_id);
        }
        if ($pidx = $request->product_idx) {
            $query = $query->where('product_idx', $pidx);
        }

        $deProdData = $query->paginate($limit)->toArray();
        $return = [];
        $data = $deProdData['data'];
        $sku_ids = array_column($data, 'sku_id');
        if ($sku_ids) {
            $stockService = new StockService();
            list(, , $stock_infos) = $stockService->getStockAll($sku_ids);
//            foreach ($stock_infos as $k => $stock_info) {
//                foreach ($stock_info as $i => $v) {
//                    if (in_array($i,['channel1','channel2','channel3','lock_channel1','lock_channel2','lock_channel3'])) $stock_infos[$k][$i] = $v;
//                }
//            }
        }
        foreach ($data as &$one) {
            $one['stock_info'] = empty($stock_infos[$one['sku_id']]) ? ['is_share' => '1', 'stock' => 0] : $stock_infos[$one['sku_id']];
        }
        $return['pageData'] = $data;
        $return['count'] = $deProdData['total'];

        return $this->success($return);
    }

    /**
     * 获取SKU.
     */
    public function getSku(Request $request)
    {
        $skuIdx = $request->skuIdx;
        $skuInfo = Sku::getSkuInfoById($skuIdx, true);
        $skuInfo['spec_type'] = explode(',', $skuInfo['spec_type']);
        $skuInfo['stockinfo'] = StockService::getSkuStock($skuInfo['sku_id']);
        $return = [];
        $return['data'] = $skuInfo;
        return json_encode($return);
    }

    public function add(Request $request)
    {
        $all = $request->all();

        $fields = [
            'sku_id' => 'required',
            'ori_price' => 'required',
            'product_idx' => 'required',
            'revenue_type' => 'required',
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required' => '请输入:attribute', // :attribute 字段占位符表示字段名称
            ]
        );
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        if (!empty($all['ori_price']) && ($all['ori_price'] < 0)) return $this->error("价格不合法");

        $product_idx = $request->product_idx;
        $pService = new ProductService();
        $product = Spu::getProductInfoById($product_idx);
        $product = $pService->formatProduct($product);
        if (empty($product)) return $this->error(0, "商品缺失");
        $pSpecType = $product['spec_type'] ?? [];

        $pSpecType = array_filter($pSpecType);
        if ($pSpecType) {
            $sTypes = Spec::batchGetSpecBySpecTypes($pSpecType);
            $fieldMap = Sku::SPEC_FIELD_MAP;
            foreach ($pSpecType as $sType) {
                $specs = (isset($sTypes[$sType])&&$sTypes[$sType]) ? array_column($sTypes[$sType], 'spec_code') : [];
                $field = $fieldMap[$sType];
                if (!empty($all[$field]) && !in_array($all[$field], $specs)) {
                    $spec_code = $all[$field];
                    Spec::insertSpec($spec_code, $sType);
                }
            }
        }
        if ($contained_sku_ids = $all['contained_sku_ids']) {
            $c_ids = explode(',', $contained_sku_ids);
            foreach ($c_ids as $c_id) {
                if (!is_numeric($c_id)) return $this->error(0, "包含Sku 格式不正确");
            }
        }
        $skuModel = new Sku();
        //如果是虚拟商品则为skuid增加虚拟商品前缀
        if(array_get($all,'virtual')==1){
            $all['sku_id'] = Sku::VIRTUAL_PREFIX.$all['sku_id'];
            //如果是虚拟商品 库存为手动配置
            $skuModel->control_stock = 1;
        }elseif(array_get($all,'unreal')==1){
            $all['sku_id'] = Sku::UNREAL_PREFIX.$all['sku_id'];
            //如果是虚拟商品 库存为手动配置
            $skuModel->control_stock = 1;
        }elseif(empty($all['ori_price'])){
            //价格为0的商品库存为手动配置
//            $skuModel->control_stock = 1;
        }

        $skuModel->sku_id = $all['sku_id'];
        $skuModel->size = $all['size'] ?? '';
        $skuModel->spec_color_code = $all['spec_color_code'];
        $skuModel->spec_capacity_ml_code = $all['spec_capacity_ml_code'];
        $skuModel->spec_capacity_g_code = $all['spec_capacity_g_code'];
        $skuModel->spec_color_code_desc = $all['spec_color_code_desc'] ?? '';
        $skuModel->spec_capacity_ml_code_desc = $all['spec_capacity_ml_code_desc'] ?? '';
        $skuModel->spec_capacity_g_code_desc = $all['spec_capacity_g_code_desc'] ?? '';
        $skuModel->ori_price = $all['ori_price'];
        $skuModel->product_idx = $all['product_idx'];
        $skuModel->contained_sku_ids = $all['contained_sku_ids'];
        $skuModel->include_skus = $all['include_skus'];
        $skuModel->revenue_type = $all['revenue_type'];
        try {
            if ($skuModel->save()) return $this->success("创建商品集合成功");
            return $this->error(0, "新增失败");
        } catch (\Exception $e) {
            return $this->error(0, $e->getMessage());
        }

    }

    /**
     * 编辑SKU.
     */
    public function editSku(Request $request)
    {
        $skuIdx = $request->id;
        $all = $request->all();

        $fields = [
            'ori_price' => 'required',
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required' => '请输入:attribute', // :attribute 字段占位符表示字段名称
                'min' => ':attribute至少:min位字符长度',
            ]
        );
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        if (!empty($all['ori_price']) && ($all['ori_price'] < 0)) return $this->error(0, "价格不合法");

        $product_idx = $request->product_idx;
        $pService = new ProductService();
        $product = Spu::getProductInfoById($product_idx);
        $product = $pService->formatProduct($product);
        if (empty($product)) return $this->error(0, "商品缺失");
        $pSpecType = $product['spec_type'] ?? [];
        $pSpecType = array_filter($pSpecType);
        if ($pSpecType) {
            $sTypes = Spec::batchGetSpecBySpecTypes($pSpecType);
            $fieldMap = Sku::SPEC_FIELD_MAP;
            foreach ($pSpecType as $sType) {
                $specs = (isset($sTypes[$sType])&&$sTypes[$sType]) ? array_column($sTypes[$sType], 'spec_code') : [];
                $field = $fieldMap[$sType];
                if (!empty($all[$field]) && !in_array($all[$field], $specs)) {
                    $spec_code = $all[$field];
                    Spec::insertSpec($spec_code, $sType);
                }
            }
        }

        if ($contained_sku_ids = $all['contained_sku_ids']) {
            $c_ids = explode(',', $contained_sku_ids);
            foreach ($c_ids as $c_id) {
                if (!is_numeric($c_id)) return $this->error("包含Sku 格式不正确");
            }
        }
        $data = [
            'spec_color_code' => $all['spec_color_code'],
            'spec_color_code_desc' => $all['spec_color_code_desc'],
            'spec_capacity_ml_code' => $all['spec_capacity_ml_code'],
            'spec_capacity_ml_code_desc' => $all['spec_capacity_ml_code_desc'],
            'spec_capacity_g_code' => $all['spec_capacity_g_code'],
            'spec_capacity_g_code_desc' => $all['spec_capacity_g_code_desc'],
            'ori_price' => $all['ori_price'],
            'size' => $all['size'] ?? '',
            'product_idx' => $all['product_idx'],
            'contained_sku_ids' => $all['contained_sku_ids'],
            'include_skus' => $all['include_skus'],
            'revenue_type' => $all['revenue_type'],
            'control_stock' => $all['control_stock'],
        ];
        try {
            $upNum = Sku::where('id', $skuIdx)->update($data);
            if ($upNum) return $this->success("更新sku成功");
            return $this->error("更新sku失败");
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 编辑sku 安全库存数量及状态
     */
    public function updateSkuSecure(Request $request)
    {
//        try {
            $skuIdx = $request->id;
            $data = $request->all();
            $skuifo = StockService::getSkuStock($skuIdx);
            //如果设置没有安全库存
            if (0 == $data['is_secure']) {

                if ($skuifo['is_secure'] == $data['is_secure']) {
                    return $this->error(0, '您未做任何修改');
                }
                //安全库存 => 不设置安全库存 若安全库存存在则分配给总库存或者渠道库存
                list($success, $message) = StockService::setNotSecure($skuIdx);

            } else {
                if ($data['secure'] <= 0) {
                    return $this->error(0, '安全库存不能小于1，请重新设置');
                }

                //安全库存 => 不设置安全库存 若安全库存存在则分配给总库存或者渠道库存
                list($success, $message) = StockService::setSecure($skuIdx, $data['secure']);


            }
            if ($success) {
                return $this->success([], $message);
            }

            return $this->error(0, $message);


//        } catch (\Exception $e) {
//            Log::error('SkuController  updateSkuSecure Method Error' . $e->getMessage());
//            return $this->error(0, '服务器繁忙，请稍后再试');
//        }


    }


    /**
     * 编辑渠道SKU库存
     */
    public function updateChannelStock(Request $request)
    {
        $skuIdx = $request->id;
        $data = $request->all();

        $skuifo = StockService::getSkuStock($skuIdx);
        $channel_stock = [];

        //如果不共享
        if (0 == $data['is_share']) {
            foreach ($skuifo['info'] as &$v) {
                $percent = $data['percent' . $v['id']];
                if ($percent < 0) {
                    return $this->error(0, '渠道比例数值不允许为负数');
                }
                $v['percent'] = $data['percent' . $v['id']];
                $channel_stock['channel' . $v['id']] = $data['channel' . $v['id']];
            }
            //判断是否重新修改了渠道比例
            //将共享库存分配给渠道库存
            if ($data['stock_tag'] != 1) {
                $channel_stock = [];

            }
            list($success, $message) = StockService::setNotShare($skuIdx, $skuifo['info'], $channel_stock);


        } else {


            if($data['increment_tag'] == 1){
                list($success, $message) = StockService::setStockIncre($skuIdx,$data['stock']);
            }else{
                list($success, $message) = StockService::setShare($skuIdx);
            }

        }

        if ($success) {
            return $this->success([], $message);
        }

        return $this->error(0, $message);

    }

    /**
     * 获取SKU库存
     */
    public function stock(Request $request)
    {
        $skuId = $request->sku_id;

        $ProductHelp = new ProductHelp();
        $skusStore = $ProductHelp->redisModel->_hget(config('redis.store'), $skuId);

        $return = [];
        $return['inventory'] = false !== $skusStore ? (int)$skusStore : 0;

        return json_encode($return);
    }


    /**
     *
     * 推送库存记录列表
     */
    public function stockLoglist(Request $request)
    {

        $limit = $request->limit ?: 10;
        $sku_logs = StockLog::orderBy('id', 'DESC');
        if ($request->sku) {
            $sku_logs = $sku_logs->where('sku_id', $request->sku);
        }
        if ($request->start) {
            $sku_logs = $sku_logs->where('created_at', '>', $request->start);
        }
        if ($request->end) {
            $sku_logs = $sku_logs->where('created_at', '<=', $request->end);
        }
        $data = $sku_logs->paginate($limit)->toarray();

        $return = [];
        $return['pageData'] = $data['data'];
        $return['count'] = $data['total'];

        return json_encode($return);
    }

    /*
     * 为jack提供的保存 kv_images
     * */
    public function saveDetail(Request $request)
    {
        $all = $request->all();

        $fields = [
            'kv_images' => 'required',
            'id' => 'required',
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required' => '请输入:attribute', // :attribute 字段占位符表示字段名称
            ]
        );

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $id = $all['id'];
        $kv_images = json_decode($all['kv_images'], true) ?? [];

        $db_kv_images = $kv_images ?? [];
        Sku::where('id', $id)->update(['kv_images' => json_encode($db_kv_images)]);
        return $this->success("", '更新成功');

    }

    /*
     * 为jack提供的获取 kv_images
     * */
    public function getDetail(Request $request)
    {

        $fields = [
            'id' => 'required',
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required' => '请输入:attribute', // :attribute 字段占位符表示字段名称
            ]
        );

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $detail = Sku::getSkuInfoById($request->id);
        $ret['kv_images'] = json_decode($detail['kv_images'], true) ?? [];

        return $this->success($ret, '更新成功');

    }

    /**
     * 获取SKU库存
     */
    public function stockAll(Request $request)
    {
        $data = Sku::getAllStock();
        return $this->success($data, 'success');
    }

    public function exportSkuInfo(Request $request)
    {
        $data = Sku::exportAllStock();
        return $this->success($data, 'success');
    }

    public function insertStock(Request $request)
    {

        $status = Sku::insertStocks($request->json_str);

        if($status){
            $data = [
                'code'=>1,
                'message'=>'success'
            ];
            return json_encode($data);

        }else{
            $data = [
                'code'=>0,
                'message'=>'fail',
            ];
            return json_encode($data);
        }


    }

    /**
     * 导入商品sku（有数）历史数据
     */
    public function exportSkuHistory(Request $request)
    {
        $data = Sku::exportSkuHistory();
        return $this->success($data, 'success');
    }

    /**
     * 导入sku（有数）历史数据
     */
    public function exportSalesInfoHistory(Request $request)
    {
        $data = Sku::exportSalesInfoHistory();
        return $this->success($data, 'success');
    }

}
