<?php

namespace App\Http\Middleware;

use App\Service\UsersService;
use App\Support\Token;
use Exception;
use Validator;
use Closure;

class OAuthUser
{
    /**
     * 校验用户登陆，如果未登陆，根据openid生成一个临时用户ID（购物车加购商品使用）
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
            $openid = $request->input('openid');
            $uid = Token::checkToken($token);
            if( ($uid === false) && empty($openid)) {
                throw new Exception("身份验证失败，请重新登录！", 2);
            }
            $request->user_id = empty($uid)?0:$uid;
            if(!$request->user_id) $request->tmp_user_id = UsersService::getTmpUserId($openid);
            return $next($request);

        } catch (Exception $e) {
            return response()->errorAjax($e);

        }
       
    }
}
