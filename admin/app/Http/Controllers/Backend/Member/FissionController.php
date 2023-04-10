<?php

namespace App\Http\Controllers\Backend\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Redis;

class FissionController extends Controller
{   
    public function list()
    {
        return view('backend.fission.index', []);
    }
    public function update()
    {   
        $response = $this->curl('member/fission/detail', request()->all());
        $data = $response['data'][0];
        $data['condition_value'] = $data['condition_value']?explode(',',$data['condition_value']):[];
        $data['value_id'] = $data['value_id']?explode(',',$data['value_id']):[];
         
        return view('backend.fission.update', ['detail' =>$data ??[]]);
    }

    public function dataList(){
        $response = $this->curl('member/fission/dataList',request()->all());
        return $response;
    }
    public function  edit()
    {
        return view('backend.fission.edit');
    }
    public function  add()
    {
        $info = request()->all();
        $response = $this->curl('member/fission/add',request()->all());
        return $response;
    }


    public function log()
    { 
        $response = $this->curl('member/fission/log');
        return view('backend.member.fissionLog', ['list' => $response['data']]);
    }

    public function active()
    {
        $data= ['id' => request('id'), 'status' =>2];
        $response=$this->curl('member/fission/active',$data);
        return $response;
    
    }

    public function unactive()
    {
        $data= ['id' => request('id'), 'status' =>3];
        $response = $this->curl('member/fission/active',$data);
        return $response;
    }


    public function view()
    {
        $response = $this->curl('member/fission/detail', request()->all());
        return view('backend.fission.view', ['detail' =>$response['data'][0] ??[]]);

    }
   
}
