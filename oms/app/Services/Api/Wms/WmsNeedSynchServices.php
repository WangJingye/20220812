<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/10/8
 * Time: 19:19
 */

namespace App\Services\Api\Wms;

use Illuminate\Support\Facades\Redis;
use App\Services\Api\Wms\WmsOssClient as WmsOssClient;

class WmsNeedSynchServices
{

    /**
     * 遍历OSS里的文件，如果往Redis里插入成功，则告知是有新文件了。
     * @return bool
     */
    public function getFileList()
    {
        $new_file_flag = false;
        $wmsOssClient = new WmsOssClient();
        $objectList = $wmsOssClient->listWmsRecFile();
        if($objectList != null){
            $redis = Redis::connection();
            $redis_set_name = "wms_new_file_" . date("Ymd");
            foreach ($objectList as $objectInfo) {
                $redis_result = $redis->sadd($redis_set_name, $objectInfo->getKey());
                if($redis_result){
                    $new_file_flag = true;
                }
            }
            return $new_file_flag;
        }
        else{
            return false;
        }
    }
}