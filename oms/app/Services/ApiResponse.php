<?php

namespace App\Services;

use Illuminate\Validation\ValidationException as ExceptionContract;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Log;

/**
 * 响应
 */
class ApiResponse
{
    /**
     * 前端接口异常返回
     *
     * @author Jason
     * @date   2017-03-02
     * @param  \Exception  $exception 异常
     * @return array
     */
    public static function errorAjax(Exception $exception)
    {
        $error = $exception->getMessage();
        Log::info(
            "response",
            [
                'code'     => $exception->getCode(),
                'response' => $error,
            ]
        );
        return [
            'code'    => $exception->getCode(),
            'message' => $error,
            'data'    => []
        ];
    }
    /**
     * 前端接口成功返回
     *
     * @author Jason
     * @date   2017-03-02
     * @param  \Exception  $exception 异常
     * @return array
     */
    public static function ajax($message, $data = [])
    {   
        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }
        $result = [
            'code' => 1,
            'message' => $message
        ];
        return $result + ['data' => $data];
    }

    /**
     * Api错误返回
     *
     * @author Jason
     * @date   2017-03-02
     * @param  array  $error 异常
     * @param  integer     $code  状态
     * @return array
     */
    public static function errorApi($error)
    {
        return [
            'status' => 0,
            'message' => $error,
        ];
    }

    /**
     * api返回
     *
     * @author Jason
     * @date   2017-03-02
     * @param  mixed     $content 返回内容
     * @param  integer   $status  状态
     * @return array
     */
    public static function api($content)
    {
        if ($content instanceof Arrayable) {
            $content = $content->toArray();
        }

        $result = [
            'status' => 1,
            'code' => 0
        ];

        return $result + ['data' => $content];
    }

    /**
     * 前端接口异常返回
     *
     * @author Jason
     * @date   2017-03-02
     * @param  \Exception  $exception 异常
     * @return array
     */
    public static function errorQmAjax(Exception $exception)
    {
        $error = $exception->getMessage();
        Log::info(
            "qm.response",
            [
                'code'     => $exception->getCode(),
                'response' => $error,
            ]
        );

        return [
            'error_response'=>[
//                'sub_msg'=>array_get($error,'message'),
//                'code'=>(string)array_get($error,'code',50),

                'sub_msg'=>$error,
                'code'=>$exception->getCode(),
                'sub_code'=>'service error',
                'msg'=>'Remote service error',
            ]
        ];
    }
}
