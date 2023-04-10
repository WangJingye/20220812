<?php
namespace App\Lib;

use OSS\OssClient;
use OSS\Core\OssException;
use App\Model\ConfigOss;

class Oss{
    private $accessKeyId;
    private $accessKeySecret;
    public  $endpoint;
    public  $bucket;
    private $ossClient;

    public function __construct()
    {
        $this->accessKeyId=env('ACCESS_KEY_ID','');
        $this->accessKeySecret=env('ACCESS_KEY_SECRET','');
        $this->endpoint=env('ENDPOINT','');
        $this->bucket=env('BUCKET','');
        $this->ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
    }

    /**
     * @param $toFile (远程文件路径)
     * @param $localFile (本地文件路径)
     * @return bool
     */
    public function upload($toFile,$localFile){
        try{
            $result = $this->ossClient->uploadFile($this->bucket, $toFile, $localFile);
            if($result['info']['http_code']==200){
                return true;
            }
            throw new OssException('上传错误');
        } catch(OssException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function delete($toFile){
        try{
            $this->ossClient->deleteObject($this->bucket, $toFile);
        } catch(OssException $e) {
            return false;
        }
        return true;
    }

    public function has($toFile){
        try{
            $this->ossClient->doesObjectExist($this->bucket, $toFile);
        } catch(OssException $e) {
            return false;
        }
        return true;
    }


    public function fileList()
    {
        try {
            $listObjectInfo = $this->ossClient->listObjects($this->bucket);
            $objectList = $listObjectInfo->getObjectList();
            if (!empty($objectList)) {
                foreach ($objectList as $objectInfo) {
                print($objectInfo->getKey() . "\t" . $objectInfo->getSize() . "\t" . $objectInfo->getLastModified() . "\n");
                }
            }
        } catch (OssException $e) {
            print $e->getMessage();
        }
    }

    public function putObject($object,$content){
        try {

            $this->ossClient->putObject($this->bucket, $object, $content);
        } catch (OssException $e) {
            print $e->getMessage();
        }
    }
}