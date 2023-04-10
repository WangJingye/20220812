<?php

namespace App\Http\Controllers\Outward\Goods;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Service\Goods\CacheService;

class CacheController extends Controller
{
    public function clear(Request $request){
        $action = $request->get('action');
        $resp = call_user_func([CacheService::class,"{$action}_clear"]);
        if($resp===true){
            return response()->json(['code'=>1,'message'=>'OK']);
        }return response()->json(['code'=>0,'message'=>$resp]);
    }


}
