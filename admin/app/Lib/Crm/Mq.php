<?php
namespace App\Lib\Crm;

class Mq
{
    protected $server;

    public function crm($api,$params){
        $res = [
            'RequestData'=>$params,
            'RespondData'=>'empty',
        ];
        try{
            if(!$this->server){
                $this->connect();
            }
            $result = $this->server->send($api,json_encode($params),array("amq-msg-type" => "text"));
            $res['RespondData'] = $result;
        }catch(\Exception $e){
            $res['Exception'] = $e->getMessage();
        }finally{
            log_json('crm',$api,json_encode($res,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            return ($res['RespondData']==1)?true:false;
        }
    }

    protected function connect(){
        $mq_url = option_get('config_page_crm_mq_url');
        $this->server = new \Stomp($mq_url);
    }



}