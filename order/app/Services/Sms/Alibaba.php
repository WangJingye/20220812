<?php namespace App\Services\Sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Alibaba
{
    public $sign = '鬼塚虎';
    public function __construct(){
        AlibabaCloud::accessKeyClient(env('ALIBABA_ACCESSKEYID'), env('ALIBABA_ACCESSKEYSECRET'))
            ->regionId(env('ALIBABA_REGIONID'))->asDefaultClient();
    }

    /**
     * 登录场景
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function login($mobile,$params){
        $sign = $this->sign;
        $template = 'SMS_190720476';
        $params = json_encode($params);
        return $this->send(compact('sign','mobile','template','params'));
    }

    /**
     * 注册场景
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function register($mobile,$params){
        $sign = $this->sign;
        $template = 'SMS_190725406';
        $params = json_encode($params);
        return $this->send(compact('sign','mobile','template','params'));
    }

    /**
     * 忘记密码
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function forgot($mobile,$params){
        $sign = $this->sign;
        $template = 'SMS_190274485';
        $params = json_encode($params);
        return $this->send(compact('sign','mobile','template','params'));
    }

    /**
     * 到货通知
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function notify($mobile,$params){
        $sign = $this->sign;
        $template = 'SMS_189031732';
        $params = json_encode($params);
        return $this->send(compact('sign','mobile','template','params'));
    }

    /**
     * 取消订单前X分钟预警
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function orderCancelBefore($mobile,$params){
        $sign = $this->sign;
        $template = 'SMS_190274489';
        $params = json_encode($params);
        return $this->send(compact('sign','mobile','template','params'));
    }

    /**
     * 取消订单时提醒
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function orderCancel($mobile,$params){
        $sign = $this->sign;
        $template = 'SMS_190720484';
        $params = json_encode($params);
        return $this->send(compact('sign','mobile','template','params'));
    }

    /**
     * 发货提醒
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function orderShipped($mobile,$params){
        $sign = $this->sign;
        $template = 'SMS_190720481';
        $params = json_encode($params);
        return $this->send(compact('sign','mobile','template','params'));
    }

    /**
     * 退货审核通过提醒
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function orderReturnAllow($mobile,$params){
        $sign = $this->sign;
        $template = 'SMS_190725433';
        $params = json_encode($params);
        return $this->send(compact('sign','mobile','template','params'));
    }

    /**
     * 退款成功提醒
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function orderRefunded($mobile,$params){
        $sign = $this->sign;
        $template = 'SMS_190725429';
        $params = json_encode($params);
        return $this->send(compact('sign','mobile','template','params'));
    }

    /**
     * @param $data
     * @return bool
     * @throws \Exception
     */
    protected function send($data){
        $log = ['RequestData'=>$data];
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'SignName' => (string)$data['sign'],
                        'PhoneNumbers' => (string)$data['mobile'],
                        'TemplateCode' => (string)$data['template'],
                        'TemplateParam'=> (string)$data['params']
                    ],
                ])->request()->toArray();
            //记录日志
            $log['RespondData'] = $result;
            log_json('sms',$log);

            if($result['Code']=='OK'){
                return true;
            }elseif($result['Code']=='isv.BUSINESS_LIMIT_CONTROL'){
                return 0;
            }
            throw new \Exception($result['Message']);
        } catch (ClientException $e) {
            throw new \Exception($e->getErrorMessage());
        } catch (ServerException $e) {
            throw new \Exception($e->getErrorMessage());
        } catch (\Exception $e) {
            $log['Error'] = $e->getMessage();
            log_json('sms',$log);
            return false;
        }
    }

    /**
     * @param $signName
     * @return array|bool
     * @throws \Exception
     */
    public function getSign($signName){
        try {
            return AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('QuerySmsSign')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'SignName' => "{$signName}",
                    ],
                ])->request()->toArray();
        } catch (ClientException $e) {
            throw new \Exception($e->getErrorMessage());
        } catch (ServerException $e) {
            throw new \Exception($e->getErrorMessage());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $templateCode
     * @return array|bool
     * @throws \Exception
     */
    public function getSmsTemplate($templateCode){
        try {
            return AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('QuerySmsTemplate')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'TemplateCode' => "{$templateCode}",
                    ],
                ])->request()->toArray();
        } catch (ClientException $e) {
            throw new \Exception($e->getErrorMessage());
        } catch (ServerException $e) {
            throw new \Exception($e->getErrorMessage());
        } catch (\Exception $e) {
            return false;
        }
    }
}
