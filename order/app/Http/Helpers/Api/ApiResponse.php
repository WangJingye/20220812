<?php

namespace App\Http\Helpers\Api;

trait ApiResponse
{
    /**
     * @param $data
     * @return mixed
     */
    public function respond($data){
        //返回时统一记录接口入参出参日志
        $requestUri = explode('/',request()->getRequestUri());
        $method = end($requestUri);
        $RequestData = request()->all();
        $RespondData = $data;
        $log = compact('RequestData','RespondData');
        log_json('api',$method,json_encode($log,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        return response()->json($data);
    }

    public function success($data=[], $msg = '成功'){
        return $this->respond(['code'=>1,'msg'=>$msg,'data'=>$data]);
    }

    public function error($msg = '失败'){
        return $this->respond(['code'=>0,'msg'=>$msg]);
    }

    /**
     * 验证错误
     * @param string $msg
     * @return mixed
     */
    public function fail($msg = '失败'){
        return $this->respond(['code'=>0,'msg'=>$msg]);
    }

}