<?php

namespace App\Http\Controllers\Backend\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class SkuController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.goods.sku.index', [
            'query_string' => http_build_query($all)
        ]);
    }

    public function get(Request $request)
    {
        $data = $this->curl('goods/sku/getSku', $request->all());
        $detail = $data['data'];
        $stock = $detail['stockinfo'];
        return view('backend.goods.sku.edit', [
            'detail' => array_to_object($detail),
            'stockinfo' => $stock
        ]);
    }

    public function add(Request $request)
    {
        $params = [];
        $params['virtual'] = ($request->get('type')=='virtual')?1:0;
        $params['unreal'] = ($request->get('type')=='unreal')?1:0;
        return view('backend.goods.sku.add',$params);
    }

    public function insert(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('goods/sku/add', $postData);
            return $response;
        }
    }

    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token']);
            $editBack = $this->curl('goods/sku/editSku', $postData);
            return $editBack;
        }
    }

    public function list()
    {
        $response = $this->curl('goods/sku/list', request()->all());
        return $response;
    }

    public function getStock()
    {
        $response = $this->curl('goods/sku/stock', request()->all());

        $detail = [];
        $detail['inventory'] = $response['inventory'];
        $detail['sku_id'] = request()->all()['sku_id'];

        return view('backend.goods.sku.stock', ['detail' => array_to_object($detail)]);
    }

    public function updateStock(Request $request)
    {
        $sku = $request->get('sku');
        $qty = (int)$request->get('qty');
        //1赠0减
        $increment = ($qty<0)?0:1;
        $username = auth()->user()->name;
        $params = [
            'sku_json'=>json_encode([[$sku,$qty,'']]),
            'channel_id'=>1,
            'increment'=>$increment,
            'note'=>"操作人员:{$username}",
        ];
        $response = $this->curl('outward/update/batchStockForce', $params);
        if(($increment==1) && ($response['code']==1)){
            $this->curl('inner/arrivalReminder', ['skus_str'=>$sku]);
        }

        return $response;
    }

    public function updateSkuSecure(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token']);
            $response = $this->curl('goods/channel/updatesecure', $postData);
            return $response;
        }
    }

    public function updateChannelStock(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token']);
            $response = $this->curl('goods/channel/update', $postData);

            return $response;
        }
    }

    public function stockLoglist()
    {
        $response = $this->curl('goods/stock/log', request()->all());

        return $response;
    }

    public function cms()
    {
        $result = $this->curl('goods/sku/getDetail', ['id' => request('id')]);

        $kv_images = '[]';
        if ($result['code'] == 1 && isset($result['data']['kv_images'])) {
            $kv_images = json_encode($result['data']['kv_images']);
        }
        return view('backend.goods.sku.cms', compact('kv_images'));
    }

    public function cmssave()
    {
        $data = [
            'id' => request('id'),
            'kv_images' => request('kv_images', request('kv_images')),
        ];
        $result = $this->curl('goods/sku/saveDetail', $data);
        return $result;
    }


    public function _export(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $all['limit'] = 1000;
        $data = $this->curl('goods/sku/infoAll', $all);
        $list = $data['data'];
        $columns = $list[0];
        unset($list['0']);
        $result = [
            'columns' => $columns,
            'value' => array_values($list),
        ];
        return $this->success($result, 'success');
    }
}
