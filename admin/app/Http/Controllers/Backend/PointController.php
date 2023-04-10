<?php

namespace App\Http\Controllers\Backend;

use App\Lib\Http;
use App\Model\Author;
use App\Model\ConfigOss;
use App\Model\OssConfig;
use App\Model\Taxonomy;
use Illuminate\Contracts\Routing\UrlGenerator;
use App\Model\Pages;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{

    function index(){
        return view('backend.point.list');
    }

    function dataList(){
        return $this->curl('pointmall/dataList',request()->all());
    }
    function get(){
         if(request('id')){
            $item=$this->curl('pointmall/get',request()->all());
            return view('backend.point.get',['detail'=>$item['data']]);
         }else{
             return view('backend.point.get',['detail'=>['id'=>'','type'=>'','status'=>'','product_sku'=>'','coupon_id'=>'','name'=>'','image'=>'','exchange_point'=>'','qty'=>'']]);
         }
    }
    function post(){
        return $this->curl('pointmall/post',request()->all());
    }
}