<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/12
 * Time: 10:31
 */

namespace App\Http\Requests;

use App\Http\Requests\Helps\Rules;
use Illuminate\Foundation\Http\FormRequest;

class InputOrderFromCartRequest extends FormRequest
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
        $regex = Rules::Regex();
        return [
//            "order" => ['required'],
////            "order.orderId" => ['required'],
////            'order.posID'=> ['required'],
        ];
    }

    public function attributes():array{
        return [
//            'order'=>'请确定所传入的数据为Json格式',
//            "order.posID" =>'posID为必填项',

        ];
    }
}
