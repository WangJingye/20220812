<?php
namespace App\Http\Requests;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
class TopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "app_key" => "required",
            "format" => "required",
            "sign_method" => "required",
            "method" => "required",
            "timestamp" => "required",
            "partner_id" => "required",
            "sign" => "required",
        ];
    }

//    public function failedValidation($request, Exception $exception)
//    {
//        //如果路由中含有“api/”，则说明是一个 api 的接口请求
//        if($request->is("router/*")){
//            //如果错误是 ValidationException的一个实例，说明是一个验证的错误
//            if($exception instanceof ValidationException){
//                $result = [
//                    "code"=>422,
//                    //这里使用 $exception->errors() 得到验证的所有错误信息，是一个关联二维数组，所以使用了array_values()取得了数组中的值，而值也是一个数组，所以用的两个 [0][0]
//                    "msg"=>array_values($exception->errors())[0][0],
//                    "data"=>""
//                ];
//                return response()->json($result);
//            }
//        }
//        return parent::render($request, $exception);
//    }

}
