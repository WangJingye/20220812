<?php

namespace App\Http\Controllers\Backend\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class DoorSkuController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        return view('backend.goods.doorSku.index');
    }

    public function get(Request $request)
    {
        $data = $this->curl('goods/doorSku/getSku', $request->all());
        $detail = $data['data'];

        return view('backend.goods.doorSku.edit', ['detail' => array_to_object($detail)]);
    }

    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token']);
            $editBack = $this->curl('goods/doorSku/editSku', $postData);

            return $editBack;
        }
    }

    public function list()
    {
        $response = $this->curl('goods/doorSku/list', request()->all());

        return $response;
    }
}
