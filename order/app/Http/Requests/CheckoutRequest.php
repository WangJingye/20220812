<?php
namespace App\Http\Requests;

use App\Http\Requests\Helps\Rules;

class CheckoutRequest extends RequestAbstract
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
            'shippingMethod' => ['required','int'],
            'addressId'=> ['required','int'],
            'paymentMethod'=>['required'],
            'invoice.name'=>['nullable',array_get($regex,'invoice.name')],
            'invoice.code'=>['nullable',array_get($regex,'invoice.code')],
        ];
    }

    public function attributes():array{
        return [
            'addressId'=>'配送地址',
            'invoice.name'=>'名称',
            'invoice.code'=>'纳税人识别号',
        ];
    }

}
