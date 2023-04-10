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

use App\Tools\Http;
use App\Model\ThrAccessInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class BaiWangAuthApi extends AuthApiBase
{
    private static $apiUri = 'router/rest';
    private static $redisTokenKey = 'HASH:SISLEY:ACCESS_TOKEN';
    private static $instance;

    private static $accessConfigs = [
        '10001455' => [
            'appKey' => '10001455',
            'appSecret' => '59882ace-3074-40ba-9371-aead1eb7ed40',
            'username' => 'admin_3000000404271',
            'password' => 'dlcdp200821!',
            'userSalt' => '56628960f3a449748f1a8579015c44f2',
        ],
    ];

    private function __construct()
    {
        self::$apiUri = env('BAIWANG_API_HOST') . self::$apiUri;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance) || !(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取接口调用令牌
     * @param $appKey
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function newAccessToken($appKey)
    {
        if (!isset(self::$accessConfigs[$appKey])) {
            throw new \Exception("{$appKey} is an invalid value.");
        }
        $config = self::$accessConfigs[$appKey];
        $postParams = [
            'method' => 'baiwang.oauth.token',
            'grant_type' => 'password',
            'client_id' => $config['appKey'],
            'client_secret' => $config['appSecret'],
            'username' => $config['username'],
            'password' => self::md5AndSha($config['password'], $config['userSalt']),
            'version' => '3.0',
            'timestamp' => time()
        ];

        $OAuthResult = Http::httpRequest($postParams, self::$apiUri, 'POST');
        if (empty($OAuthResult)) {
            throw new \Exception("Get accessToken fail.");
        }
        $exception = self::storeAccessToken($appKey, $OAuthResult);
        if ($exception === false) {
            throw new \Exception("Store accessToken fail.");
        }
    }

    /**
     * 刷新接口调用令牌
     * @param $appKey
     * @param $tokenData
     * @return mixed|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshAccessToken($appKey, $tokenData)
    {
        if (!isset(self::$accessConfigs[$appKey])) {
            throw new \Exception("{$appKey} is an invalid value.");
        }
        $config = self::$accessConfigs[$appKey];
        $postParams = [
            'method' => 'baiwang.oauth.token',
            'grant_type' => 'refresh_token',
            'client_id' => $config['appKey'],
            'client_secret' => $config['appSecret'],
            'refresh_token' => $tokenData['authorizer_refresh_token'],
            'version' => '3.0',
            'timestamp' => time()
        ];

        $OAuthResult = Http::httpRequest($postParams, self::$apiUri, 'POST');
        if (empty($OAuthResult)) {
            throw new \Exception("Get accessToken fail.");
        }
        if (isset($OAuthResult['errorResponse']) && !empty($OAuthResult['errorResponse'])) {
            if (isset($OAuthResult['errorResponse']['code']) && $OAuthResult['errorResponse']['code'] === 100006) {
                return self::newAccessToken($appKey);
            }
        } else {
            $exception = self::storeAccessToken($appKey, $OAuthResult);
            if ($exception === false) {
                throw new \Exception("Store accessToken fail.");
            }
        }
    }

    /**
     * 存储接口调用令牌
     * @param $appKey
     * @param $tokenData
     * @return mixed|void
     */
    public function storeAccessToken($appKey, $tokenData)
    {
        $response = $tokenData['response'];

        $update['authorizer_appid'] = $appKey;
        $update['authorizer_access_token'] = $response['access_token'];
        $update['expires_in'] = $response['expires_in'];
        $update['authorizer_refresh_token'] = $response['refresh_token'];
        $update['expired_at'] = date('Y-m-d H:i:s', time() + $response['expires_in'] - 200);
        $exception = DB::transaction(function () use ($appKey, $update) {
            ThrAccessInfo::updateOrCreate(
                ['authorizer_appid' => $appKey],
                $update
            );
        });

        Redis::hset(self::$redisTokenKey, $appKey, $response['access_token']);

        return is_null($exception) ? true : false;
    }

    /**
     * 从数据库获取接口调用令牌
     * @param $appKey
     * @return string
     */
    public function getAccessToken($appKey)
    {
        $authTokenRow = ThrAccessInfo::where('authorizer_appid', $appKey)->get()->toArray();
        if (empty($authTokenRow)) {
            return '';
        } else {
            return $authTokenRow[0]['authorizer_access_token'];
        }
    }

    /**
     * md5+sha-1加密
     * @param $pwd 明文密码
     * @param $salt 用户盐值
     * @return string
     */
    private static function md5AndSha($pwd, $salt)
    {
        return sha1(md5($pwd . $salt));
    }
}
