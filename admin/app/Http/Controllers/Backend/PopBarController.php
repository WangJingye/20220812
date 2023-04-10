<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

class PopBarController extends Controller
{

    public function index(){

        return view('backend.popBar.index');
    }
    public function popList(){
        $result = $this->curl('member/popInfo',['backend' =>1]);
        //$data['data']['data'] = $result['data'];
        foreach ($result['data'] as $value){
            $data['data'][] = json_decode($value,true);
        }
        $data['code'] = 0;

        return $data;
        //return view('backend.popBar.index',['detail' => $data]);
    }

    public function barList()
    {

    }
    public  function barAdd()
    {

    }


    function popAdd(){
        if(request('id')){
            $item=$this->curl('pointmall/get',request()->all());
            return view('backend.popBar.add',['detail'=>$item['data']]);
        }else{
            return view('backend.popBar.add',['detail'=>['id'=>'','type'=>'','status'=>'','product_sku'=>'','coupon_id'=>'','name'=>'','image'=>'','exchange_point'=>'','qty'=>'']]);
        }
    }
    public function add(){



    }



    function post(){
        return $this->curl('pointmall/post',request()->all());
    }
}
