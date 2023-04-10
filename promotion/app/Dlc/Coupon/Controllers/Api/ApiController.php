<?php namespace App\Dlc\Coupon\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;

class ApiController extends BaseController
{
    /**
     * 成功
     * @param array $data
     * @param string $msg
     * @return array
     */
    public function success($data=[],$msg = '成功'){
        return [
            'code'=>1,
            'message'=>$msg,
            'data'=>$data
        ];
    }

    /**
     * 失败
     * @param string $msg
     * @return array
     */
    public function error($msg = '失败'){
        return [
            'code'=>0,
            'message'=>$msg,
        ];
    }
}
