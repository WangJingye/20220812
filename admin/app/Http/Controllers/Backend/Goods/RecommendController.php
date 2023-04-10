<?php

namespace App\Http\Controllers\Backend\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class RecommendController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        return view('backend.goods.recommend.index');
    }

    public function add(Request $request){
        return view('backend.goods.recommend.add');
    }


    public function insert(Request $request)
    {
//        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('goods/recommend/add', $postData);

            return $response;
//        }
    }

    public function changeStatus(Request $request)
    {
//        if ($request->isMethod('post')) {
            $postData = $request->all();
            $changeBack = $this->curl('goods/recommend/changeStatus', $postData);

            return $changeBack;
//        }
    }

    public function list()
    {
        $response = $this->curl('goods/recommend/list', request()->all());

        return $response['data'];
    }

}
