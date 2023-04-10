<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/8/1
 * Time: 11:44
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkinRequest extends FormRequest
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
    public function rules(){
        return [
            'openid' => ['required'],
        ];
    }

    public function messages(){
        return [
            'required'=>':attribute为必填项',
        ];
    }

    public function attributes(){
        return [
            'openid'=>'openid',
            'url'=>'图片url',
        ];
    }
}