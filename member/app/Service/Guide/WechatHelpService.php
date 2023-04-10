<?php

/**
 * User: JIWI001
 * Date: 2019/11/27
 * Time: 22:00.
 */

namespace App\Service\Guide;


class WechatHelpService
{
    public function generateQrCode($scene, $page, $width, $isHyaline = false)
    {
        $WechatService = new WechatService();
        $info = [];
        $info['scene'] = $scene;
        $info['page'] = $page; 
        $info['width'] = $width;
        $info['is_hyaline'] = $isHyaline;
        $return = $WechatService->wxApi('https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=', json_encode($info, JSON_UNESCAPED_UNICODE));
        \Log::info('太阳码参数='.json_encode($info));
        $deReturn = json_decode($return, true);
        if (isset($deReturn['errcode'])) {
            return ['code' =>0, 'data' => $deReturn ?? []];
        } else {
            $realPath = '../public/static/qr/' . md5($scene) . '.jpg';
            file_put_contents($realPath, $return);

            return ['code'=> 1 ,'fileName' => md5($scene) . '.jpg', 'path' => $realPath];
        }
    }
}
