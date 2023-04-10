<?php namespace App\Lib;

use Illuminate\Support\Facades\Log;

class Oss{

    public $client;
    public $bucket;

    public function __construct()
    {
        $secretId = env('OSS_ACCESS_KEY_ID'); //"云 API 密钥 SecretId";
        $secretKey = env('OSS_ACCESS_KEY_SECRET'); //"云 API 密钥 SecretKey";
        $region = env('OSS_REGION'); //设置一个默认的存储桶地域
        $this->bucket = env('OSS_BUCKET');
        $this->client = new \Qcloud\Cos\Client(
            [
                'region' => $region,
                'schema' => 'https', //协议头部，默认为http
                'credentials'=> [
                    'secretId'  => $secretId ,
                    'secretKey' => $secretKey
                ]
            ]
        );
    }

    /**
     * @param $toFile (远程文件路径)
     * @param $localFile (本地文件路径)
     * @return bool
     */
    public function upload($toFile,$localFile){
        try {
            if(!file_exists($localFile)){
                throw new \Exception('本地文件不存在');
            }
            $file = fopen($localFile, 'rb');
            if ($file) {
                $this->client->Upload(
                    $bucket = $this->bucket,
                    $key = $toFile,
                    $body = $file
                );
                return true;
            }throw new \Exception('上传失败');
        } catch (\Exception $e) {
            $this->errorLog($e);
            return false;
        }
    }

    public function delete($toFile){
        try {
            $this->client->deleteObject(array(
                'Bucket' => $this->bucket,
                'Key' => $toFile,
                'VersionId' => 'string'
            ));
            // 请求成功
            return true;
        } catch (\Exception $e) {
            $this->errorLog($e);
            // 请求失败
            return false;
        }
    }

    public function has($toFile){
        try {
            $this->client->getObject(array(
                'Bucket' => $this->bucket,
                'Key' => $toFile,
            ));
            // 请求成功
            return true;
        } catch (\Exception $e) {
            $this->errorLog($e);
            // 请求失败
            return false;
        }
    }

    public function putObject($path,$content){
        try {
            $this->client->putObject(array(
                'Bucket' => $this->bucket,
                'Key' => $path,
                'Body' => $content
            ));
            return true;
        } catch (\Exception $e) {
            $this->errorLog($e);
            return false;
        }
    }

    protected function errorLog(\Exception $e){
        Log::error('cos error',[
            'message'=>$e->getMessage(),
            'file'=>$e->getFile(),
            'line'=>$e->getLine(),
        ]);
    }
}