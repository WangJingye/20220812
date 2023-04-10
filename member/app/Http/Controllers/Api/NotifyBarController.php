<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Exception;
use App\Model\Help;
use Illuminate\Support\Facades\Log;

class NotifyBarController extends Controller
{

    public  function  __construct()
    {
        $this->redis = app('redis');
    }

    /**
     * @return array|mixed
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function info(){
        try{
            $notifyBar    =   $this->redis->lrange('notifyBar',0,0);
            if (empty($notifyBar)){
                return response()->ajax(false,[]);
            }
            return response()->ajax(true,json_decode($notifyBar[0]));
        }catch (Exception $e)
        {
            return response()->errorAjax($e);
        }
    }
    /**
     * @param Request $request
     * @return array|mixed
     */
    public function add(Request $request)
    {
        try{
            $this->redis->lpush('notifyBar', json_encode($request->all()));
            return $this->success(true);
        }catch (Exception $e){
            return response()->errorAjax($e);
        }
    }

    /**
     * @return mixed
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public  function infoPop(Request $request){
        try{
            $data = $request->all();
            if(isset($data['backend']))
            {
                $popInfo    =   $this->redis->lrange('popInfo',0,10);
                return response()->ajax(true,$popInfo);
            }else{
                $popInfo    =   $this->redis->lrange('popInfo',0,0);
                $popInfo = $popInfo[0];
            }
            if (empty($popInfo)){
                return response()->ajax(false,[]);
            }
            return response()->ajax(true,json_decode($popInfo));
        }catch (Exception $e)
        {
            return response()->errorAjax($e);
        }
    }
    /**
     * @param Request $request
     * @return array|mixed
     */
    public function addPop(Request $request)
    {
        try{
            $this->redis->lpush('popInfo', json_encode($request->all()));
            return $this->success(true);
        }catch (Exception $e){
            return response()->errorAjax($e);
        }
    }
    public function PopBar()
    {
        try{
            $notifyBar    =   $this->redis->lrange('notifyBar',0,0);
            $popInfo    =   $this->redis->lrange('popInfo',0,0);
            $result['bar'] = json_decode($notifyBar[0] ??'',true);
            $time = date('Y-m-d H:i:s');
        if($result['bar']['status'] == 0)
        {
            $result['bar'] = [];
        }else
        {
            $result['bar']['status'] = 0;
            if(( $time >=$result['bar']['start'] ) &&  ($time <= $result['bar']['end']  ) )
            {
                $result['bar']['status'] = 1;
            }
        }
        $result['pop'] = json_decode($popInfo[0] ?? '',true);
            if($result['pop']['pop_status'] == 0)
            {
                $result['pop'] = [];
            }else
            {
                $result['pop']['pop_status'] = 0;
                if(( $time >=$result['pop']['start'] ) &&  ($time <= $result['pop']['end']  ) )
                {
                    $result['pop']['pop_status'] = 1;
                }
            }
        if(($result['pop']['start'] < date('Y-m-d H:i:s') )&& ($result['pop']['end'] > date('Y-m-d H:i:s') ))
        {
            $result['pop']['pop_status'] = 1;
        }
            //$result = array_merge(json_decode($notifyBar[0] ??'',true),json_decode($popInfo[0] ?? '',true));
            return response()->ajax(true,$result);
        }catch (Exception $e){
            return response()->errorAjax($e);
        }
    }
}