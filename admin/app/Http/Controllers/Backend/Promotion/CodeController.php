<?php

namespace App\Http\Controllers\Backend\Promotion;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use App\Model\Promotion\Category;
use App\Http\Controllers\Backend\Promotion\CartController;


class CodeController extends CartController
{
    
    function __construct(){
        parent::__construct();
    }
    
    
    public function index(Request $request){
        return view('backend.promotion.code.index');
    }
    public function edit(Request $request){
        $id= request('id');
        $detail = $this->curl('promotion/code/get',request()->all())['data']??[];
        $type = $this->type;
        $getType= $detail['type']??request('type');
        $detail['getType'] = ['type'=>$getType,'name'=>$type[$getType]];
        $categoryData= $this->curl('category/tree')['data']??[];
        return view('backend.promotion.code.edit',['detail'=>$detail,'categoryData'=>$categoryData,]);
    }
    public function dataList(){
        $response = $this->curl('promotion/code/dataList',request()->all());
        return $response;
    }
    
    public function post(){
        $response=$this->curl('promotion/code/post',request()->all());
        return $response;
    }

}
