<?php


namespace App\Support;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Sms
{


    public function send($phone, $msg_type,$code)
    {
        $client = new Client();
        $url = 'http://arvatoapi.10690007.net/proxysms/mt';
        $ms = $this->getTemplate($msg_type,$code);
        $dc = 15;
        $sm = mb_convert_encoding($ms, "GBK", "auto");
        $sm = bin2hex($sm);
        $params = [
            'command' => 'MT_REQUEST',
            'spid' => '6961',
            'sppassword' => 'xslEC6961P',
            'spsc' => '00',
            'sa' => '10',
            'da' => '86' . $phone,
            'dc' => $dc,
            'sm' => $sm,

        ];
        $response = $client->request('post', $url, ['form_params' => $params]);
        Log::info('send_msg_request:',$params);

        $code = $response->getStatusCode();

        if ($code == 200) {

            $contents = $response->getBody()->getContents();
            Log::info('send_msg_response:'.$contents,[]);
            parse_str($contents, $response_array);
            if ($response_array['mtstat'] == 'ACCEPTD' && $response_array['mterrcode'] === '000') {
                return true;
            }
            return false;
        }
        return false;
    }



    /**
     *
     *
     */
    public function getTemplate($msg_type,$code)
    {
        switch ($msg_type) {
            case "1":
                $ms = "您申请登录的验证码为：{$code}，请于5分钟内使用，为了您的账户安全请勿告知他人。";
                break;
            case "2":
                $ms = "您申请注册的验证码为：{$code}，请于5分钟内使用，为了您的账户安全请勿告知他人。";
                break;
            case "3":
                $ms = "您申请重置密码的验证码为：{$code}，请于5分钟内使用，为了您的账户安全请勿告知他人。";
                break;
            case "4":
                $ms = "【法国希思黎】初遇年轻之美
感谢您注册成为法国希思黎中国官方网站会员！
源自法国的高科技植物精粹品牌Sisley法国希思黎，始终坚持以高科技的方式提取植物中有效的活性成分，经过精准的配比生产出安全、有效、愉悦的产品。现为您献上新客呵宠礼遇，已发放至您的账户中，首次购物即可尊享。即刻登录官网http://suo.im/66BYI9，体验匠心而成的肌肤奢护艺术。
注：每人仅限尊享1次。回TD退订";
                break;
            case "5":
                $ms = "{$code}为您的验证码，请于5分钟内使用，为了您的账户安全请勿告知他人。";
                break;
            default:
                $ms = "";
        }
        return $ms;
    }

}