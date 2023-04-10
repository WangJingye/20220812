<?php namespace App\Service\Dlc;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Service\Dlc\SftpFile;
use App\Lib\Oss;

class WxService
{
    /**
     * @param $page
     * @param $scene
     * @return string
     * @throws \Exception
     */
    public function getQrCode($page,$scene){
        $md5 = md5($page.$scene);
        $filename = $md5.'.jpg';
        $remote_path = 'tp_upload/static/qrcode/'.$filename;
        $has = Redis::hexists('qrcode',$md5);
        if(!$has){
            $filepath = $this->generateQrCode($page,$scene);
            $res = (new Oss)->upload($remote_path,$filepath);
            @unlink($filepath);
            if(!$res){
                throw new \Exception('生成错误');
            }
            Redis::hset('qrcode',$md5,1);
        }
        return env('OSS_DOMAIN').'/'.$remote_path;
    }

    /**
     * @param $page
     * @param $scene
     * @return string
     * @throws \Exception
     */
    public function generateQrCode($page,$scene){
        $info = [];
        $info['scene'] = $scene;
        $info['page'] = $page;
        $info['width'] = 800;
        $info['is_hyaline'] = false;
        $return = (new \App\Service\Guide\WechatService)->wxApi('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=', json_encode($info, JSON_UNESCAPED_UNICODE));
        $deReturn = json_decode($return, true);
        if(empty($deReturn['errcode'])){
            $filename = uniqid().'.jpg';
            $filepath = self::getStorageImagePath().'/'.$filename;
            if(file_put_contents($filepath,$return)){
                return $filepath;
            }
        }
        $errorMsg = implode(',',$deReturn);
        throw new \Exception($errorMsg);
    }

    protected function getStorageImagePath(){
        $path = storage_path('file/img');
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        return $path;
    }
}