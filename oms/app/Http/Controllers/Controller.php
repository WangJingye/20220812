<?php

namespace App\Http\Controllers;


use Illuminate\Routing\Controller as BaseController;
use Response;
use App\Support\Token;

class Controller extends BaseController
{

    /**
     * 创建token
     * @param string $msg
     * @param array $data
     * @return mixed
     */
    public function success($msg = '成功',$data=[],$uid=''){
        $result = ['code'=>1,'message'=>$msg,'data'=>$data];
        if($uid){
            $data = Token::createToken($uid);
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

    public function expire(){
        return Response::json(['code'=>2,'message'=>'登录失效']);
    }

    /**
     * @return mixed
     */
    public function getUid(){
        $token = app('request')->header('token')??'';
        if($token){
            $uid = Token::getUidByToken($token);
            if($uid){
                return $uid;
            }
        }return false;
    }
}
