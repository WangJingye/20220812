<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'keyword' => ['required'],
        ];
    }

    public function messages(){
        return [
            'required'=>':attribute为必填项',

        ];
    }

    public function attributes(){
        return [
            'keyword'=>'关键词',
        ];
    }
}
