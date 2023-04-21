<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exceptions\ApiPlaintextException;
use App\Support\Token;

class ApiController extends Controller
{
    public function success($data = [], $msg = 'OK')
    {
        return [
            'code' => 1,
            'message' => $msg,
            'data' => $data
        ];
    }

    public function error($data=[],$msg='error'){
        return [
            'code'=>0,
            'message'=>$msg,
            'data'=>$data
        ];
    }

    /**
     * @param int $no_exception (是否会抛出异常)
     * @return mixed
     * @throws ApiPlaintextException
     */
    public function getUid($no_exception = 0){
        $token = app('request')->header('token')??'';
        if($token){
            $uid = Token::getUidByToken($token);
            if($uid){
                return $uid;
            }
        }
        if($no_exception){
            return false;
        }throw new ApiPlaintextException('未登录',2);
    }

    public function getOpenid($no_exception = 0){
        $token = app('request')->header('token')??'';
        if($token){
            $uid = Token::getOpenidByToken($token);
            if($uid){
                return $uid;
            }
        }
        if($no_exception){
            return false;
        }throw new ApiPlaintextException('没登录',2);
    }

    /**
     * 获取渠道
     * @param int $no_exception
     * @return array|bool|string
     * @throws ApiPlaintextException
     */
    public function getFrom($no_exception = 0){
        $from = request()->header('from');
        if($from){
            return $from;
        }
        if($no_exception){
            return false;
        }throw new ApiPlaintextException('错误的渠道');
    }

    /**
     * [getRealIp 获取真实ip]
     * @Author   Peien
     * @DateTime 2020-08-13T14:48:13+0800
     * @return   [type]                   [description]
     */
    public function getRealIp()
    {
        $ip=FALSE;
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi ("^(10│172.16│192.168).", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
}
