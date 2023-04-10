<?php

namespace App\Lib;

use OSS\OssClient;
use OSS\Core\OssException;

class Oss
{
    private static $accessKeyId;
    private static $accessKeySecret;
    private static $ossClient;
    private static $endpoint;
    private static $bucket;
    private static $instance;

    private function __construct()
    {
        self::$accessKeyId = env('ACCESS_KEY_ID', '');
        self::$accessKeySecret = env('ACCESS_KEY_SECRET', '');
        self::$endpoint = env('ENDPOINT', '');
        self::$bucket = env('BUCKET', '');
        self::$ossClient = new OssClient(self::$accessKeyId, self::$accessKeySecret, self::$endpoint);
    }

    public static function getInstance()
    {
        if (is_null(self::$instance) || !(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取远程文件
     * @param $remoteFile
     * @param $localFile
     * @return bool
     */
    public function getFile($remoteFile, $localFile)
    {
        try {
            $options = [
                OssClient::OSS_FILE_DOWNLOAD => $localFile
            ];
            self::$ossClient->getObject(self::$bucket, $remoteFile, $options);

            return true;
        } catch (OssException $e) {
            return $e->getMessage();
        }
    }
}