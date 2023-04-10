<?php namespace App\Services\Sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Alibaba
{
    public function __construct(){
        AlibabaCloud::accessKeyClient(env('ALIBABA_ACCESSKEYID'), env('ALIBABA_ACCESSKEYSECRET'))
            ->regionId(env('ALIBABA_REGIONID'))->asDefaultClient();
    }

    /**
     * 注册场景
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function register($mobile,$params){
        $sign = '鬼冢虎官网';
        $template = 'SMS_188975200';
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
        $sign = '鬼冢虎官网';
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
        $sign = '鬼冢虎官网';
        $template = 'SMS_189016757';
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
        $sign = '鬼冢虎官网';
        $template = 'SMS_188992166';
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
        $sign = '鬼冢虎官网';
        $template = 'SMS_188992167';
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
    public function orderRefundAllow($mobile,$params){
        $sign = '鬼冢虎官网';
        $template = 'SMS_189031739';
        $params = json_encode($params);
        return $this->send(compact('sign','mobile','template','params'));
    }

    /**
     * 退货成功提醒
     * @param $mobile
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public function orderRefunded($mobile,$params){
        $sign = '鬼冢虎官网';
        $template = 'SMS_189026771';
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
            log_array('sms',$log);

            if($result['Code']=='OK'){
                return true;
            }
            throw new \Exception($result['Message']);
        } catch (ClientException $e) {
            throw new \Exception($e->getErrorMessage());
        } catch (ServerException $e) {
            throw new \Exception($e->getErrorMessage());
        } catch (\Exception $e) {
            $log['Error'] = $e->getMessage();
            log_array('sms',$log);
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
