<?php
/**
 *  ===========================================
 *  File Name   AuthApiBase.php
 *  Class Name  AuthApiBase
 *  Date        2020-07-28 14:13
 *  Created by  William Ji
 *  ===========================================
 **/

namespace App\Service;

abstract class AuthApiBase
{
    /**
     * 获取接口调用令牌
     * @param $appKey
     * @return mixed
     */
    abstract public function newAccessToken($appKey);

    /**
     * 刷新接口调用令牌
     * @param $appKey
     * @param $tokenData
     * @return mixed
     */
    abstract public function refreshAccessToken($appKey, $tokenData);

    /**
     * 存储接口调用令牌
     * @param $appKey
     * @param $tokenData
     * @return mixed
     */
    abstract public function storeAccessToken($appKey, $tokenData);

    /**
     * 获取接口调用令牌
     * @param $appKey
     * @return mixed
     */
    abstract public function getAccessToken($appKey);
}
