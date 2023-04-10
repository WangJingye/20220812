<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use App\Services\ApiResponse;
use Exception;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // api接口返回宏
        Response::macro('api', function ($mContent = [], $iStatus = 200, array $aHeaders = [], $iOptions = 0) {

            return response()->json(ApiResponse::api($mContent), $iStatus, $aHeaders, $iOptions);
        });

        // 错误ajax接口返回宏
        Response::macro('errorApi', function ($error) {
            return response()->json(ApiResponse::errorApi($error));
        }); 

        // 异常ajax接口返回宏
        Response::macro('errorAjax', function (Exception $mContent, $iAjaxStatus = 200, $iStatus = 200, array $aHeaders = [], $iOptions = 0) {
            return response()->json(ApiResponse::errorAjax($mContent, $iAjaxStatus), $iStatus, $aHeaders, $iOptions);
        });

        // 异常ajax接口返回宏
        Response::macro('errorQmAjax', function (Exception $mContent, $iAjaxStatus = 200, $iStatus = 200, array $aHeaders = [], $iOptions = 0) {
            return response()->json(ApiResponse::errorQmAjax($mContent, $iAjaxStatus), $iStatus, $aHeaders, $iOptions);
        });

        // ajax接口返回宏
        Response::macro('ajax', function ($mContent = [], $iAjaxStatus = 200, $iStatus = 200, array $aHeaders = [], $iOptions = 0) {
            return response()->json(ApiResponse::ajax($mContent, $iAjaxStatus), $iStatus, $aHeaders, $iOptions);
        });    
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}