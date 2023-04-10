<?php

namespace App\Http\Middleware;

use App\Model\CrmCustomers;
use Exception;
use Validator;
use Closure;

class OAuthAuthenticate
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

            $fields = [
                'openid' => 'required|string',
            ];
            $validator = Validator::make($request->all(), $fields, [
                'required' => ':attribute 为必填项',//:attribute 字段占位符表示字段名称
                'string'   => ':attribute 为字符串'
            ]);
            if($validator->fails()) {
                throw new Exception($validator->errors()->first(), 0);
            }

            $encryptString = $request->input('openid');

            $codeStatus = CrmCustomers::getToken($encryptString);
            if($codeStatus === false) {

                throw new Exception("openid 参数不合法, 请重新获取", 2);
            } else if($codeStatus === 2) {

                throw new Exception("身份已失效，请重新登录！", 2);
            } else if($codeStatus === 3) {
                throw new Exception("匿名登录，请前往登录！", 3);
            } 
            return $next($request);

        } catch (Exception $e) {
            return response()->errorAjax($e);
            
        }
       
    }
}
