<?php namespace App\Services\DLCOms;

class Response
{
    public function orderAdd($result){
        if($result->code==200){
            return true;
        }return $result->message;
    }

    public function invoiceAdd($result){
        return $result;
    }

    /**
     * 会员Code合并
     * @param $result
     * @return mixed
     */
    public function memberUpdateCode($result){
        if($result->code==200){
            return true;
        }return $result->message;
    }

    public function getLogistics($result){
        $result = (array)$result;
        return $result;
    }

}