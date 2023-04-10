<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Model\Goods\DoorSku;
use App\Model\Goods\ProductHelp;

class DoorSkuController extends Controller
{
    protected $_model = Spu::class;

    /**
     * SKU列表，支持模糊查询.
     */
    public function list(Request $request)
    {
        $limit = $request->limit ?: 10;
        if ($request->prodId) {
            $enSkuData = DoorSku::where('sku', 'like', '%' . $request->prodId . '%')->paginate($limit)->toJson();
        } else {
            $enSkuData = DoorSku::paginate($limit)->toJson();
        }
        $deSkuData = json_decode($enSkuData, true);

        $return = [];
        $return['pageData'] = $deSkuData['data'];
        $return['count'] = $deSkuData['total'];

        return json_encode($return);
    }

    /**
     * 获取SKU.
     */
    public function getSku(Request $request)
    {
        $skuIdx = $request->skuIdx;
        $skuInfo = DoorSku::where('id', $skuIdx)->first()->toArray();
        $return = [];
        $return['data'] = $skuInfo;

        return json_encode($return);
    }

    /**
     * 编辑SKU.
     */
    public function editSku(Request $request)
    {
        $skuIdx = $request->id;
        $updateData = $request->all();
        unset($updateData['id']);
        $exception = DB::transaction(function () use ($skuIdx, $updateData) {
            DoorSku::updateOrCreate(
                ['id' => $skuIdx],
                $updateData
            );
        });

        if (is_null($exception)) {
            return $this->success([]);
        } else {
            return $this->error(0, $exception);
        }
    }

}
