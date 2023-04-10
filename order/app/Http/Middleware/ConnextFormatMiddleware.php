<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;
use App\Exceptions\ApiPlaintextException;

class ConnextFormatMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string $type
     * @param  string $format
     * @return mixed
     */
    public function handle($request, Closure $next, $type = 'default', $format = 'json'){
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $next($request);
        $original = $response->getOriginalContent();
        //如果是验证错误或错误码为指定的code 则展示该错误的信息
        $error = call_user_func(function() use($response){
            if($response->exception){
                if($response->exception instanceof ValidationException){
                    //格式化验证错误
                    if(!empty($response->exception->validator)){
                        $msg = array_reduce($response->exception->validator,function($result,$item){
                            $result .= (implode(',',$item).',');
                            return $result;
                        });
                        $msg = rtrim($msg,',');
                    }
                    return [
                        'msg'=>$msg??'网络异常'
                    ];
                }elseif($response->exception instanceof ApiPlaintextException){
                    return [
                        'code'=>$response->exception->getCode(),
                        'msg'=>$response->exception->getMessage(),
                    ];
                }return ['msg'=>'网络异常'];
            }return false;
        });
        $resp = $original;
        //如有异常则根据类型返回相应的数据
        if($error){
            if($type == 'top'){
                //恒康接口
                $resp = [
                    'error_response'=>[
                        'sub_msg'=>array_get($error,'msg'),
                        'code'=>(string)array_get($error,'code',50),
                        'sub_code'=>'service error',
                        'msg'=>'Remote service error',
                    ]
                ];
            }elseif($type == 'api'){
                //前端接口
                $resp = [
                    'code'=>array_get($error,'code',0),
                    'message'=>array_get($error,'msg')
                ];
                //前端接口若有异常仍然固定返回200
                $response->setStatusCode(200);
            }
        }
        //根据format返回格式默认json
        if($format == 'xml'){
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            $resp = array2Xml($resp);
            $response->header("Content-type","text/xml");
        }elseif($format == 'html'){
            $response->header("Content-type","text/html");
        }
        if($response instanceof \Illuminate\Http\JsonResponse){
            $response->setData($resp);
        }else{
            $response->setContent($resp);
        }
        return $response;
    }
}
