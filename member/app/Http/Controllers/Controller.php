<?php

namespace App\Http\Controllers;


use App\Lib\Http;
use Illuminate\Routing\Controller as BaseController;
use Response;
use App\Support\Token;

class Controller extends BaseController
{
    protected $user_id = 0;

    protected $http;

    /**
     * 创建token
     * @param string $msg
     * @param array $data
     * @return mixed
     */
    public function success($msg = '成功',$data=[],$uid='',$open_id=''){
        $result = ['code'=>1,'message'=>$msg,'data'=>$data];
        if($uid){
            $data = Token::createToken($uid,$open_id);
            if(!$data){
                return $this->error('登录异常');
            }
            $result = array_merge($result,$data);
        }
        return Response::json($result);
    }

    /**失败
     * @param string $msg
     * @param array $data
     * @return mixed
     */
    public function error($msg = '失败', $data=[]){
        return Response::json(['code'=>0,'message'=>$msg,'data'=>$data]);
    }

    public function __construct(){

        $this->http = new Http();
        if($token = app('request')->header('token')??''){
            $this->user_id = Token::getUidByToken($token);
        }
    }

    public function expire(){
        return Response::json(['code'=>2,'message'=>'身份失效']);
    }

    public function eventExpire(){
        return Response::json(['code'=>1,'data'=>[
            'message'=>'活动不存在',
            'status'=>2,
        ]]);
    }
}
