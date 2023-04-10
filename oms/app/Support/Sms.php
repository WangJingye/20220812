<?php


namespace App\Support;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Sms
{


    public function send($phone, $msg_type, $name = '', $order_sn = '')
    {
        return true;
        $client = new Client();
        $url = 'http://arvatoapi.10690007.net/proxysms/mt';
        $ms = $this->getTemplate($msg_type, $name, $order_sn);
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
        $code = $response->getStatusCode();
        Log::info('send_message_request',$params);
        if ($code == 200) {

            $contents = $response->getBody()->getContents();
            Log::info('send_message_response' . $contents);
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
    public function getTemplate($msg_type, $code, $order_sn)
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
                $ms = "初遇年轻之美
感谢您注册成为法国希思黎中国官方网站会员！
源自法国的高科技植物精粹品牌Sisley法国希思黎，始终坚持以高科技的方式提取植物中有效的活性成分，经过精准的配比生产出安全、有效、愉悦的产品。现为您献上新客呵宠礼遇：登陆http://suo.im/66BYI9 首次任意购物，结账时输入代码“Welcome” 即可尊享明星礼盒，体验匠心而成的肌肤奢护艺术。
注：每人仅限尊享1次。回TD退订";
                break;
            case "5":
                $ms = "{$code}为您的验证码，请于5分钟内使用，为了您的账户安全请勿告知他人。";
                break;
            case "6":
                $ms = "尊敬的{$code}，感谢您在法国希思黎官网订购，您的订单为{$order_sn}，我们会及时处理并安排发货。您也可登录http://suo.im/6tVcR9 查看。如有任何疑问，请联系官网在线客服，期待您再次光临！回TD退订";
                break;
            case "7":
                $ms = "您的订单{$order_sn}由于在4小时内未完成付款已被自动取消。您也可登录法国希思黎官网 http://suo.im/5vHMTy 重新购买，期待您的再次光临。回TD退订";
                break;
            case "8":
                $ms = "您的订单{$order_sn}由于用户申请取消，已取消成功。您也可登录法国希思黎官网 http://suo.im/5vHMTy 重新购买，期待您的再次光临。回TD退订";
                break;
            case "9":
                $ms = "亲爱的顾客，我们留意到您在法国希思黎官网有一笔订单{$order_sn}尚未完成，请立即前往“我的订单”支付， 奢享官方限时礼遇。http://suo.im/6moYZq 如需帮助，请联系官网在线客服，如您已经支付，请忽略此信息。回TD退订";
                break;
            case "10":
                $ms = "亲爱的顾客，您的订单{$order_sn}已发货，请注意查收，您也可登录法国希思黎官网查看 http://suo.im/5K4fu0 ，如有任何疑问，请联系官网在线客服，期待您再次光临！回TD退订";
                break;
            case "11";
                $ms = "亲爱的顾客，您的售后订单{$order_sn}已完成退款，请及时查收，您也可登录法国希思黎官网 http://suo.im/67n38K 查看，如有任何疑问，请联系官网在线客服，期待您再次光临！回TD退订";
                break;
            default:
                $ms = "";
        }
        return $ms;
    }

}