<?php

namespace App\Http\Controllers\Backend\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class CollectionController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        return view('backend.goods.collection.index');
    }

    public function get(Request $request)
    {
        $data = $this->curl('goods/collection/get', $request->all());
        $detail = $data['data'];
        $cats = $detail['cats'];

        return view('backend.goods.collection.edit', ['detail' => array_to_object($detail),'cats'=>$cats]);
    }

    public function update(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('goods/collection/update', $postData);
            return $response;
        }
    }

    public function add(Request $request){
        return view('backend.goods.collection.add');
    }

    public function getFormatedProductList(Request $request){
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('goods/collection/getFormatedProductList', $postData);
            return $response['data'];
    }

    public function insert(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('goods/collection/insert', $postData);

            return $response;
        }
    }

    public function changeStatus(Request $request)
    {
//        if ($request->isMethod('post')) {
            $postData = $request->all();
            $changeBack = $this->curl('goods/collection/changeStatus', $postData);

            return $changeBack;
//        }
    }

    public function list()
    {
        $response = $this->curl('goods/collection/list', request()->all());

        return $response['data'];
    }

    public function cms(){
        $result = $this->curl('goods/collection/getDetail',['id'=>request('id')]);

        $kv_images='[]';
        $pc='[]';
        $wechat='[]';
        if($result['code']==1 && isset($result['data']['kv_images'])){
            $kv_images=$result['data']['kv_images'];
        }
        if($result['code']==1 && isset($result['data']['pc'])){
            $pc=$result['data']['pc'];
        }
        if($result['code']==1 && isset($result['data']['wechat'])){
            $wechat=$result['data']['wechat'];
        }
        return view('backend.goods.collection.cms',compact('kv_images','pc','wechat'));
    }

    public function cmssave(){
        $data=[
            'id'=>request('id'),
            'wechat'=>request('content')['h5'],
            'pc'=>request('content')['h5'],
            'kv_images'=>request('kv_images',request('kv_images')),
        ];
        $result=$this->curl('goods/collection/saveDetail',$data);
        return $result;
    }

}
