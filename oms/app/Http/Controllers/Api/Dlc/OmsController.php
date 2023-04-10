<?php namespace App\Http\Controllers\Api\Dlc;

use App\Http\Controllers\Controller;
use Validator;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dlc\OmsServices;
use App\Services\DLCOms\Config as OmsConfig;
use App\Exceptions\DlcOmsServiceParamsException;

class OmsController extends Controller
{
    public function service(Request $request,$method='')
    {
        $params = $request->all();
        $resp = $this->handle($params,$method);
        $response = [
            'code'=>$resp['code'],
            'message'=>OmsConfig::RESPONSE_CODE[$resp['code']],
        ];
        Log::info('OmsService',[
            'requestData'=>$params,
            'method'=>$method,
            'respData'=>$response,
        ]);
        return Response::json($response);
    }

    private function handle($data,$method){
        $errorMsg = '';
        $code = call_user_func(function() use($data,$method,&$errorMsg){
            try{
                if(request()->header('test')==1 && env('OMS_SERVICE_TEST')==1){
                    //允许测试越过签名
                    $data['sign'] = $this->getSignature($data);
                }
                $sign = array_get($data,'sign');
                unset($data['sign']);
                if($sign==$this->getSignature($data)){
                    if(method_exists(OmsServices::class,$method)){
                        if(empty($data['params'])){
                            return 403;
                        }
                        $params = json_decode($data['params'],true);
                        call_user_func([OmsServices::class,$method],$params);
                        return 200;
                    }return 404;
                }return 402;
            }catch (DlcOmsServiceParamsException $e){
                $errorMsg = $e->getMessage();
                return 300;
            }catch (\Exception $e){
                $errorMsg = $e->getMessage();
                return 500;
            }
        });
        $this->log([
            'RequestData'=>$data,
            'RespondCode'=>$code,
            'ErrorMessage'=>$errorMsg,
        ]);
        return [
            'code'=>$code,
        ];
    }

    private function getSignature($params){
        $app_key = config('dlc.dlc_oms_app_key');
        $app_secret = config('dlc.dlc_oms_app_secret');
        $params['app_key'] = $app_key;

        ksort($params);
        $sign='';
        foreach($params as $k=>$v){
            $sign.=$k.'='.$v.'&';
        }
        return strtoupper(md5($sign.$app_secret));
    }

    private function log($data){
        log_array('dlc_oms_service',$data);
    }

    public function syncStock(Request $request){
        try{
            $params = $request->all();
            $v = Validator::make($params, [
                'sku_json' => 'required',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            OmsServices::LvmhSiteSyncGoodsStock($params['sku_json'],1);
            return $this->success();
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }
}
