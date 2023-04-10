<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/12
 * Time: 10:19
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\InputOrderFromCartRequest;

class InputOrderController extends ApiController
{
    public function NewOrderFromCart(InputOrderFromCartRequest $request){
        return $request;
    }
}
