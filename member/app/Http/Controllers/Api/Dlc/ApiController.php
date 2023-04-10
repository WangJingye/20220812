<?php namespace App\Http\Controllers\Api\Dlc;

use App\Http\Controllers\Controller;
use App\Exceptions\ApiPlaintextException;
use App\Support\Token;

class ApiController extends Controller
{
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


}
