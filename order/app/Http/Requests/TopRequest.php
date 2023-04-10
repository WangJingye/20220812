<?php
namespace App\Http\Requests;

class TopRequest extends RequestAbstract
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

}
