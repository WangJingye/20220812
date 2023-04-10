<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/10/8
 * Time: 20:31
 */

namespace App\Services\Api\Wms;

use OSS\OssClient;
use OSS\Core\OssException;

class WmsOssClient
{

    //将美图返回的原始解析结果保留一份测试结果到OSS
    public function listWmsRecFile(){
        $accessKeyId = "LTAI4G6YKzZHP8L2jEVQnXMR";
        $accessKeySecret = "XrDcadt5SqvXwwBqkkn1K3oxQlffRe";
        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = "http://oss-cn-shanghai.aliyuncs.com";
        // 存储空间名称
        $bucket= "dlc-wms";

        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $file_date = date("Ymd");
        $prefix = 'upload/EC_REC_' . $file_date;
        $delimiter = '/';
        $nextMarker = '';
        $maxkeys = 100;
        $options = array(
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $maxkeys,
            'marker' => $nextMarker,
        );
        try {
            $listObjectInfo = $ossClient->listObjects($bucket, $options);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
        $objectList = $listObjectInfo->getObjectList(); // object list
        if (!empty($objectList)) {
            return $objectList;
        }
        else{
            return null;
        }

    }
}