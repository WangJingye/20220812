<?php

namespace App\Http\Controllers\Backend\Ad;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class ItemController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $all = $request->all();
        $tpl = $request->tpl;
        $tpl = $tpl?:'index';
        $data = $this->curl('ad/item/list', $request->all());
        $list = $data['data']["pageData"]??[];
        $loc_id = empty($all['loc_id'])?$data['data']['loc_id']:$all['loc_id'];

        unset($all['_url']);
        return view('backend.ad.item.'.$tpl,[
            'query_string'=>http_build_query($all),
            'list_json'=>json_encode($list),
            'loc_id'=>$loc_id,
        ]);
    }

    public function list(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('ad/item/list', $request->all());
        return $data['data'];
    }

    //热销榜单
    public function hotsale(Request $request){
        $request['loc'] = 'hot_sale_product';
        $request['tpl'] = 'hotSale';
        return $this->index($request);
    }

    public function edit(Request $request)
    {
        $data = $this->curl('ad/loc/list', $request->all());
        $detail = $data['data'];
        $stock = $detail['stockinfo'];

        return view('backend.goods.sku.edit', [
            'detail' => array_to_object($detail),
            'stockinfo'=>$stock
        ]);
    }

    public function add(Request $request)
    {
        return view('backend.goods.sku.add');
    }

    public function insert(Request $request)
    {
        $postData = $request->all();
        unset($postData['file']);
        $response = $this->curl('ad/item/insert', $postData);
        return $response;
    }

    public function update(Request $request)
    {
        $postData = $request->all();
        unset($postData['_token']);
        $editBack = $this->curl('ad/item/update', $postData);
        return $editBack;
    }

    public function delete(Request $request)
    {
        $postData = $request->all();
        $editBack = $this->curl('ad/item/delete', $postData);
        return $editBack;
    }

}
