<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Model\ConfigOss;
use Validator;
use Exception;

class ConfigController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }
  
    public function oss()
    {
        $data = ConfigOss::find(1);
        return view('backend.config.oss',['data' => $data]);
    }

    public function ossSave(Request $request)
    {
        try {
            $rules = [
                'access_key_id' => 'required|string|size:100',
                'access_key_secret' => 'required|string|size:100',
                'endpoint' => 'required|string|size:100',
                'bucket' => 'required|string|size:50',
            ];

            $messages = [
                'required' => 'The :attribute field is required.',
                'string'   => 'The :attribute must be string',
                'size'     => 'The :attribute must be :value chars'
            ];

            Validator::make($request->all(), $rules, $messages);
            $access_key_id = $request->input('access_key_id');
            $access_key_secret = $request->input('access_key_secret');
            $endpoint = $request->input('endpoint');
            $bucket = $request->input('bucket');
            $url = $request->input('url');
            $active = $request->input('active', 0);

            ConfigOss::query()->updateOrInsert(['id'=>1],compact('access_key_id','access_key_secret','endpoint','bucket','url','active'));
            return ['code' => 0, 'message' => '保存成功'];
        } catch (Exception $e) {
            return ['code' => -1, 'message' => $e->getMessage()];
        }

    }
    public function redis()
    {
        $items=[];
        return view('backend.config.oss',['items'=>$items]);
    }
    public  function save()
    {
        
    }
}
