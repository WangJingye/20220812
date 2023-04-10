<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Exceptions\ApiPlaintextException;

class ApiController extends Controller
{
    /**
     * 需要登录
     * @return array
     */
    public function expire(){
        return [
            'code'=>2,
            'message'=>'OK',
            'data'=>[]
        ];
    }

    /**
     * @param int $no_exception (是否会抛出异常)
     * @return mixed
     * @throws ApiPlaintextException
     */
    public function getUid($no_exception = 0){
        $token = request()->header('token');
        if($token){
            $uid = UserRepository::getUidByToken($token);
            if($uid){
                return $uid;
            }
        }
        if($no_exception){
            return false;
        }throw new ApiPlaintextException('未登录',2);
    }

    public function getCookieId(){
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return session_id();
    }


}
