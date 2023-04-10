<?php
namespace App\Lib;
use App\Http\Helpers\Api\PromotionCurlLog;

class Sms{
    protected $sms_url;
    protected $sms_sign;
    protected $sms_formid;
    protected $sms_content;

    public function __construct(){
        list($this->sms_url,$this->sms_sign,$this->sms_formid,$this->sms_content)=option_get_with([
            'config_page_sms_url',
            'config_page_sms_sign',
            'config_page_sms_formid',
            'config_page_sms_content'
        ]);
    }

    public function sendVerifyCode($mobile,$code){
        $desc = $this->sms_content?:'您的验证码为：#verify_code#';
        $desc = str_replace('#verify_code#',$code,$desc);
        $content = $desc;
        $result = $this->sendSmsByArdenVendor($mobile,$content);
        return $result;
    }

    protected function sendSmsByArdenVendor($mobile,$content){
        $scheduleData = '2010-1-1';
        $curl = curl_init();
        $geturl = $this->sms_url."?mobile=".$mobile."&FormatID=".$this->sms_formid."&Content=".$content."&ScheduleDate=".$scheduleData."&TokenID=".$this->sms_sign;
        curl_setopt_array($curl,[
            CURLOPT_URL => $geturl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        ]);
        $response = curl_exec($curl);
        $return_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $logger = new PromotionCurlLog();
        $logger->curlLog($geturl,"Get" , $this->sms_url,
            "", $return_code, json_encode($response) );
        curl_close($curl);

        if(strpos($response,"OK:")!==false){
            return true;
        }
        return false;
    }

}