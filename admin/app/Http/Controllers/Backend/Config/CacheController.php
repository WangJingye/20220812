<?php namespace App\Http\Controllers\Backend\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Log;

class CacheController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.config.cache');
    }

    public function clear(Request $request,$action)
    {
        $error = 0;
        try{
            $api = app('ApiRequestInner');
            $params = [
                'action'=>$action,
            ];
            $resp = $api->request('goods/cache/clear','POST',$params);
            if($resp['code']==1){
                return true;
            }throw new \Exception('刷新失败');
        } catch (\Throwable $e){
            $error = $e->getMessage();
        } finally {
            return $error?['code'=>0,'message'=>$error]:['code'=>1,'message'=>'成功'];
        }
    }


}
