<?php
/**
 *  ===========================================
 *  File Name   Http.php
 *  Class Name  Http
 *  Date        2020-07-28 14:13
 *  Created by  William Ji
 *  ===========================================
 **/

namespace App\Tools;

use GuzzleHttp\Client;
use App\Services\CommonService;
use GuzzleHttp\RequestOptions;

class Http
{
    private static $sendParams = [
        RequestOptions::TIMEOUT => 10
    ];

    /**
     * @param array $params
     * @param string $resource
     * @param string $method
     * @param string $dType
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function httpRequest(array $params, string $resource, string $method, string $dType = ''): array
    {
        $rawRequest['resource'] = $resource;
        $rawRequest['params'] = $params;

        try {
            $client = new Client();
            $sendData = self::getSendParams($method, $params, $dType);
            $sendParams = array_merge(self::$sendParams, $sendData);
            $response = $client->request($method, $resource, $sendParams);
            $responseBody = $response->getBody()->getContents();

            CommonService::putCommonLog('获取资源结果:' . $responseBody, $rawRequest);

            return json_decode($responseBody, true);
        } catch (\Throwable $e) {
            CommonService::putCommonLog('获取资源信息失败:' . $e->getMessage(), $rawRequest, 'error');
            //TODO writed the error-msg into Log.
            return [];
        }
    }

    /**
     * 获取发送的数据
     *
     * @param string $pType
     * @param array $params
     * @param string $dType
     *
     * @return array
     */
    private static function getSendParams(string $pType, array $params, string $dType = ''): array
    {
        $pType = strtoupper($pType);
        $dType = strtoupper($dType);

        $sendParams = [];
        if ($pType == 'GET') {
            $sendParams[RequestOptions::QUERY] = $params;
        } else {
            switch ($dType) {
                case 'JSON':
                    $sendParams[RequestOptions::JSON] = $params;
                    break;
                case 'FORM':
                default:
                    $sendParams[RequestOptions::FORM_PARAMS] = $params;
                    break;
            }
        }

        return $sendParams;
    }
}
