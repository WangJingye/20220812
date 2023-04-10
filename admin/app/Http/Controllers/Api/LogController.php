<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function post()
    {
        $userId=request('user_id');
        $module=request('module');
        $action=request('action');
        $content=request('content');
        $data=[
            'user_id'=>$userId,
            'module'=>$module,
            'action'=>$action,
            'content'=>$content,
        ];
        DB::table('report_log')->insert($data);
    }
}
