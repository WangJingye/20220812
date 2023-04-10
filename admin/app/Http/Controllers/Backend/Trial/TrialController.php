<?php

namespace App\Http\Controllers\Backend\Trial;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Redis;

class TrialController extends Controller
{   
    public function index()
    {
        return view('backend.trial.index', []);
    }

    public function dataList(){
        $response = $this->curl('order/trial/dataList',request()->all());
        return $response;
    }

    public function view()
    {
        $response = $this->curl('order/trial/detail',request()->all());
        return view('backend.trial.view', ['detail' => $response['data']]);
    }


    public function edit()
    {
       $response = $this->curl('order/trial/add',request()->all());
       return $response; 
    }
    public function add()
    {
        return view('backend.trial.edit');
    }

    public function update()
    {
        $response = $this->curl('order/trial/detail',request()->all());
        return view('backend.trial.update', ['detail' => $response['data']]);
        //return $response;
    }

    public function active()
    {
        $data= ['id' => request('id'), 'status' =>2];
        $response=$this->curl('order/trial/active',$data);
        return $response;
    
    }

    public function unactive()
    {
        $data= ['id' => request('id'), 'status' =>3];
        $response = $this->curl('order/trial/active',$data);
        return $response;
    }
   
}
