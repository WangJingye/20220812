<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/8/2
 * Time: 11:48
 */

namespace App\Model\Skin;

use App\Lib\Oss;
use Oss\OssException;

class SkinOssClient
{

    //将美图返回的原始解析结果保留一份测试结果到OSS
    public function meituOriginalDataToOss($oject, $data){
        $oject_new = "MeituSkinResponse/" . date("Ymd") . "/". $oject . "_" . time() . ".json";
        $ossClient = new Oss();
        $ossClient->putObject($oject_new, $data);
        return $oject_new;
    }

    //生成的五维图上传到Oss
    public function fiveImageToOss(){

    }
}