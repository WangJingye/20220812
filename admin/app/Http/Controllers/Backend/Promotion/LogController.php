<?php

namespace App\Http\Controllers\Backend\Promotion;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class LogController extends Controller
{
    
    function __construct(){
        parent::__construct();
    }
    
    public function index(Request $request){
        return view('backend.promotion.log.index');
    }
    
    public function dataList(){
        $response = $this->curl('promotion/log/dataList',request()->all());
        return $response;
    }
   

}
