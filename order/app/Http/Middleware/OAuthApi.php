<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiPlaintextException;
use App\Support\Token;
use Exception;
use Validator;
use Closure;

class OAuthApi
{
    /**
     * Handle an incoming request.
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws ApiPlaintextException
     */
    public function handle($request, Closure $next)
    {
        try{
            if($request->header('token')){
                $request->merge(['token' => $request->header('token'),'refresh-token'=>$request->header('refresh-token')]);
            }

            $fields = [
                'token' => 'required|string',
                'refresh-token' => 'required|string',
            ];
            $validator = Validator::make($request->all(), $fields, [
                'required' => ':attribute 为必填项',//:attribute 字段占位符表示字段名称
                'string'   => ':attribute 为字符串'
            ]);
            if($validator->fails()) {
                throw new Exception('token和refresh-token必传', 0);
            }

            $token = $request->input('token');
            $refresh_token = $request->input('refresh-token');

            $codeStatus = Token::checkToken($token,$refresh_token);
            if($codeStatus === false) {
                throw new Exception("身份验证失败，请重新登录！", 2);
            }

            $black_arr = [1060479,1058208,1058207, 806982, 806981, 806980, 806979];
            if(in_array($codeStatus,$black_arr)) {
                throw new Exception("身份验证失败，请重新登录！", 2);
            }
            return $next($request);
        }catch(\Exception $e){
            throw new ApiPlaintextException($e->getMessage());
        }
    }
}
