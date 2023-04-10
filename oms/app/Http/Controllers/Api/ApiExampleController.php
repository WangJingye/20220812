<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
//use App\Services\Api\MakeCartServices;

class ApiExampleController extends ApiController
{
    public function demo(){
        $header_app_name = isset($_SERVER['HTTP_APP_NAME']) ? $_SERVER['HTTP_APP_NAME'] : null;
        $header_brand_code = isset($_SERVER['HTTP_BRAND_CODE']) ? $_SERVER['HTTP_BRAND_CODE'] : null;
        $order_info[] = $header_app_name;
        $order_info[] = $header_brand_code;
        return $order_info;
        $ms = new MakeCartServices();
        return $this->success($ms->makeOrderId());
    }
}
