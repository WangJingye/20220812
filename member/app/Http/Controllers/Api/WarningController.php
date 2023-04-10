<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;
use Exception;

class WarningController extends Controller
{
    //
    public function goldPrice(Request $request)
    {
    	try {
            $whichDay = date("w"); //星期日
            $todayFormat = date('d/m/Y');
            $jsonGoldData = Redis::get('gold-price-'.$todayFormat);
            if(empty($jsonGoldData) && $whichDay !== 0) {
            	throw new Exception("今日金价未获取到", 0);
            }
	       	return response()->ajax('success');
        } catch (Exception $e) {
        	return response()->errorAjax($e);
        }
           
    }
}
