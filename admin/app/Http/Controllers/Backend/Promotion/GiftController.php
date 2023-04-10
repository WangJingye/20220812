<?php

namespace App\Http\Controllers\Backend\Promotion;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use App\Model\Promotion\Category;
use Illuminate\Support\Facades\Storage;

class GiftController extends Controller
{
    
    function __construct(){
        parent::__construct();
    }
    
    public function index(Request $request){
        return view('backend.promotion.gift.index');
    }
    
    public function dataList(){
        $response = $this->curl('promotion/gift/dataList',request()->all());
        return $response;
    }
    public function active(){
        $data = request()->all();
        $data['userId'] = auth()->user()->id;
        $data['userEmail'] = auth()->user()->email;
        $response = $this->curl('promotion/gift/active',$data);
        return $response;
    }
    public function unactive(){
        $data = request()->all();
        $data['userId'] = auth()->user()->id;
        $data['userEmail'] = auth()->user()->email;
        $response = $this->curl('promotion/gift/unactive',$data);
        return $response;
    }
    
    public function edit(Request $request){
        $detail= $this->curl('promotion/gift/get',request()->all())['data']??[];
        if(isset($detail['pic']) and $detail['pic']){
            $detail['oss_pic'] = env('OSS_DOMAIN').'/'.$detail['pic'];
        }
        return view('backend.promotion.gift.edit',['detail'=>$detail]);
    }
    public function view(Request $request){
        $detail= $this->curl('promotion/gift/get',request()->all())['data']??[];
        if(isset($detail['pic']) and $detail['pic']){
            $detail['oss_pic'] = env('OSS_DOMAIN').'/'.$detail['pic'];
        }
        return view('backend.promotion.gift.view',['detail'=>$detail]);
    }
    public function post(){
       $return =  $this->curl('promotion/gift/post',request()->all());
       return $return;
    }
    //
    public function uploadPic(){
        $path = request()->file->store('dlc_statics');
        $local_path = storage_path().'/app/'.$path;
        $localClient = new \App\Lib\Local();
        return ['code' => 0, 'path' => $localClient->upload($path, $local_path)];
    }

}
