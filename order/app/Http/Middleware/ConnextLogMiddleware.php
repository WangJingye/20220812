<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class ConnextLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $start_time = $this->get_millisecond();
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $next($request);
        //将路由作为文件夹名称
        $dir = call_user_func(function() use($request){
            $requestUri = array_filter(explode('/',$request->getRequestUri()));
            return strtolower(implode('-',$requestUri));
        });
        //去掉后面的get参数
        $strpos = strripos($dir,"?");
        if($strpos!==false){
            $dir = substr($dir,0,$strpos);
        }
        //如果参数中携带指定参数 则将参数过滤后追加到文件夹名称中
        $method = $request->get('method');
        if($method){
            //只保留英文数字下划线
            $pattern = "/[A-Za-z_0-9]+/u";
            preg_match_all($pattern,$method,$match);
            $match = join('-', $match[0]);
            if($match){
                $dir.=('-'.$match);
            }
        }
        //记录数据
        $RequestHeader = function_exists('getallheaders')?getallheaders():$request->header();
        $RequestData = $request->all();
        $RespondData = call_user_func(function() use($response){
            if($response->exception){
                $data = [
                    'ErrorCode'=>$response->exception->getCode(),
                    'ErrorMessage'=>$response->exception->getMessage(),
                    'ErrorFile'=>$response->exception->getFile(),
                    'ErrorLine'=>$response->exception->getLine(),
                ];
                if($response->exception instanceof ValidationException){
                    $data['ErrorSubMessage'] = $response->exception->validator;
                }
            }
            return $data??[];
        })?:$response->getContent();
        $Time = ($this->get_millisecond()-$start_time).'ms';
        $log = compact('RequestHeader','Time','RequestData','RespondData');
        //记录日志
        log_json($dir,$log);
        return $response;
    }

    protected function get_millisecond(){
        $time = explode(" ", microtime());
        return intval(($time[1]+$time[0])*1000);
    }
}
