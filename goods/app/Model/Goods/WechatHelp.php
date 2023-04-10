<?php

/**
 * User: JIWI001
 * Date: 2019/11/27
 * Time: 22:00.
 */

namespace App\Model\Goods;

use App\Model\Common\WechatService;

class WechatHelp
{
    public function generateQrCode($scene, $page, $width, $isHyaline = false)
    {
        $WechatService = new WechatService();
        $info = [];
        $info['scene'] = $scene;
        $info['page'] = $page;
        $info['width'] = $width;
        $info['is_hyaline'] = $isHyaline;
        $return = $WechatService->wxApi('wxa/getwxacodeunlimit', json_encode($info, JSON_UNESCAPED_UNICODE));
        $deReturn = json_decode($return, true);
        if (isset($deReturn['errcode'])) {
            return false;
        } else {
            $realPath = '../storage/qr/'.$scene.'.jpg';
            file_put_contents($realPath, $return);

            return ['fileName' => $scene.'.jpg', 'path' => $realPath];
        }
    }
}
