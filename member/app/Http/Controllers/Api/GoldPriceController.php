<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;


class GoldPriceController extends Controller
{
    //
    public function index()
    {
    	$jsonGoldData = Redis::get('gold-price-today');
    	$data = json_decode($jsonGoldData, true);
		return response()->ajax('获取金价成功', $data);

    }
}
