<?php
namespace App\Http\Requests;

use App\Http\Requests\Helps\Rules;

class SaveAddressRequest extends RequestAbstract
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
            "id" => ['nullable','int'],
            "username" => ['nullable',array_get($regex,'username')],
            "mobile" => ['nullable',array_get($regex,'mobile')],
            "province" => ['nullable'],
            "city" => ['nullable'],
            "county" => ['nullable'],
            "detail" => ['nullable',array_get($regex,'detail')],
            "postcode" => ['nullable',array_get($regex,'postcode')],
        ];
    }

    public function attributes():array{
        return [
            'username'=>'姓名',
            'mobile'=>'手机号',
            'detail'=>'详细地址',
            'postcode'=>'邮编',
        ];
    }

}
