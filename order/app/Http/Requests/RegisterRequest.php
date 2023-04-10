<?php
namespace App\Http\Requests;

use App\Http\Requests\Helps\Rules;

class RegisterRequest extends RequestAbstract
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
            "username" => ['required',array_get($regex,'username')],
            "mobile" => ['required',array_get($regex,'mobile')],
            "sex" => ['required'],
            "code" => ['required'],
            "password" => ['required'],
            "birth" => ['nullable',array_get($regex,'birthday')],
        ];
    }

    public function attributes():array{
        return [
            'username'=>'姓名',
            'mobile'=>'手机号',
        ];
    }

}
