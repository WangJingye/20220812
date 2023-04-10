<?php
namespace App\Http\Requests;

use App\Http\Requests\Helps\Rules;

class SaveRefundInfoRequest extends RequestAbstract
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
            "id" => ['required'],
            "company" => ['required'],
            "sid" => ['required'],
        ];
    }

}
