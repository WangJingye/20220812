<?php

namespace App\Http\Controllers\Outward\Goods;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Service\Goods\DataService;

class DataController extends Controller
{
    public function import(Request $request){
        $action = $request->get('action');
        $params = $request->get('params');
        $resp = call_user_func([DataService::class,"{$action}_import"],$params);
        if($resp===true){
            return response()->json(['code'=>1,'message'=>'OK']);
        }return response()->json(['code'=>0,'message'=>$resp]);
    }


}
