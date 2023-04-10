<?php

namespace App\Http\Middleware;

use App\Support\Token;
use Exception;
use Validator;
use Closure;

class OAuthApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try {
            if($request->header('token')){
                $request->merge(['token' => $request->header('token')]);
            }
            $fields = [
                'token' => 'required|string',
            ];
            $validator = Validator::make($request->all(), $fields, [
                'required' => ':attribute 为必填项',//:attribute 字段占位符表示字段名称
                'string'   => ':attribute 为字符串'
            ]);
            if($validator->fails()) {
                throw new Exception('token必传', 0);
            }

            $token = $request->input('token');
            $codeStatus = Token::checkToken($token);
            if($codeStatus === false) {
                throw new Exception("身份验证失败，请重新登录！", 2);
            }
            $info = Token::getInfoByToken($token);
            $black_arr = [1060479,1058208,1058207, 806982, 806981, 806980, 806979];
            if(in_array($info['uid'],$black_arr)) {
                throw new Exception("身份验证失败，请重新登录！", 2);
            }
            if (array_key_exists('uid', $info)) {
                $request->user_id = $info['uid'];
            }
            if (array_key_exists('openid', $info)) {
                $request->openid = $info['openid'];
            }

            return $next($request);

        } catch (Exception $e) {
            return response()->errorAjax($e);

        }
       
    }
}
