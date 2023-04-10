<?php

namespace App\Http\Controllers\Backend\Config;

use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use Exception;
use App\Http\Controllers\Backend\Controller;
use App\Model\Config;

class CouponController extends Controller
{
    function __construct(){
        parent::__construct();
    }
    public function index(){
        $data = Config::all()->toArray();
        $detail = [
            'new_member_coupon'=>'',
        ];
        foreach($data as $item){
            $detail[$item['config_name']] = $item['config_value'];
            if($item['config_name'] == 'new_member_coupon_pic'){
                $detail['new_member_coupon_pic_url'] = env('OSS_DOMAIN').'/'. $item['config_value'];
            }
        }
        $coupon_list = $this->curl('promotion/coupon/activeList',['limit'=>100]);
        $coupon_list = $coupon_list['data'];
        $detail['coupon_list'] = $coupon_list;
        return view('backend.config.coupon',['detail' => $detail]);
    }
    public function save(){
        $data = request()->all();
        $arr = ['new_member_coupon',];
        foreach($arr as $item){
            $update = [
                'config_name'=>$item,
                'config_value'=>$data[$item],
            ];
            $config = Config::where('config_name',$item)->get()->toArray();
            if(count($config)){
                Config::where('config_name',$item)->update($update);
            }else{
                Config::insert($update);
            }
        }
        return ['code'=>1];
    }
    public function uploadPic(){
        $path = request()->file->store('dlc_statics');
        $local_path = storage_path().'/app/'.$path;
        $localClient = new \App\Lib\Local();
        return ['code' => 0, 'path' => $localClient->upload($path, $local_path)];
    }
}
