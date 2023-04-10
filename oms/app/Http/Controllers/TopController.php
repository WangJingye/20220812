<?php namespace App\Http\Controllers;

use App\Exceptions\ApiPlaintextException;
use Illuminate\Http\Request;
use App\Services\Top\TopHelper;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Model\Help;

class TopController extends Controller
{
    /**
     * 恒康接口公共入口
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function routerRest(Request $request)
    {
        try {
            $all = $request->all();
            $xml = $request->getContent();
            Help::Log('wms_request2:'.$xml,$all,'wms');
            if (!$xml) {
                throw new Exception('请求参数缺失', 50);
            }
            $params = TopHelper::xml2array($xml);

            if(!$params){
                throw new Exception('请求参数解析错误，请确认格式', 50);
            }
            $request->params = $params;
            $v = Validator::make($all, [
                "app_key" => "required",
                "sign_method" => "required",
                "method" => "required",
                "timestamp" => "required",
                "sign" => "required",

            ]);

            if ($v->fails()) {
                throw new Exception($v->errors()->first(), 50);
            }

            //将xx.xx.xx格式转换为XxXxXx
            $method = explode('.', $request->input('method'));
            array_walk($method, function (&$value, $key) {
                $value = ucfirst($value);
            });
            $method = implode('', $method);
            //签名验证
            unset($all['_url'], $all['sign']);
            if (!TopHelper::checkSign($all, $request->get('sign'), $request->get('sign_method'),$xml)) {
                throw new Exception('签名错误', 50);
            }
            //根据method实例化相应的类并执行execute
            $className = '\\App\\Services\\Top\\Method\\' . $method;
            /** @var \App\Services\Top\TopAbstract $class */
            $class = new $className($request);
            list($code, $message, $data) = $class->execute();
            if ($code == 0) {
                return $this->apiQm($code, $message, $data);
            }
            throw new Exception($message, 50);

        } catch (Exception $e) {
            Help::Log('wms_response:'.$e,[],'wms');
            return $this->errorQm($e);

        }

    }


    /**
     * 前端接口异常返回
     *
     * @param \Exception $exception 异常
     * @return array
     * @author Jason
     * @date   2017-03-02
     */
    public function errorQm(Exception $exception)
    {
        $error = $exception->getMessage();
        Log::info(
            "qm.error.response",
            [
                'code' => $exception->getCode(),
                'response' => $error,
            ]
        );

        $return = [
            'error_response' => [
//                'sub_msg'=>array_get($error,'message'),
//                'code'=>(string)array_get($error,'code',50),

                'sub_msg' => $error,
                'code' => $exception->getCode(),
                'sub_code' => 'service error',
                'msg' => 'Remote service error',
            ]
        ];
        $xml = array2Xml($return);

        $body = "<?xml version='1.0' encoding='utf-8'?><response>" . $xml . '</response>';
        return $body;
    }

    /**
     * 奇门xml 返回接口
     * @param code
     * @return array
     * @author Jason
     */
    public function apiQm($code, $message, $data)
    {

        Log::info(
            "qm.error.response",
            [
                'code' => $code,
                'message' => $message,
                'data' => $data,
            ]
        );

        $response = [
            "code" => 0,
            "flag" => "success",
            "message" => "info",
        ];

        $return['response'] = array_merge($response, $data);

        $xml = array2Xml($response);

        $body = "<?xml version='1.0' encoding='utf-8'?><response>" . $xml . '</response>';
        return $body;
    }

}
