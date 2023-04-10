<?php

namespace App\Http\Controllers\Backend\Report;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use App\Model\Promotion\Category;
use Illuminate\Support\Facades\DB;


class LogController extends Controller
{
    
    function __construct(){
        parent::__construct();
    }
    
    
    public function index(Request $request){
        return view('backend.report.log.index');
    }
    
    public function dataList(){
        $name = request('name');
        $limit = request('limit', 10);
        $page = request('page');
        $list=DB::table('report_log')
            ->orderBy('id','desc')
            ->paginate($limit)->toArray();
        
        return [
            'code'=>0,
            'data'=>$list['data'],
            'count' => $list['total'],
        ];
    }
    
    
}
