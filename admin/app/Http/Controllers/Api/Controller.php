<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Lib\Http;
use App\Model\Permission;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected $_http;
    protected $pageSize = 10;
    
    function __construct(){
       $this->_http= new Http();
       $this->pageSize=config('view.page_size');
    }
    
    
    /**
     * 
     * @param string $url
     * @param array $data
     * @return mixed
     */
    public function curl($apiName,$data=[]){
       return  $this->_http->curl($apiName,$data);
    }


    function success($data, $message = "ok")
    {

        return [
            'code' => 1,
            'data' => $data
        ];
    }

    function error($message = "fail")
    {
        return [
            'code' => 0,
            'error' => [
                'code' => 500,
                'type' => 'NORMAL',
                'message' => 'fail'
            ]
        ];
    }

    function ok($data, $message = "ok"){
        return [
            'code' => 1,
            'message'=>$message,
            'data' => $data
        ];
    }
}


