<?php

namespace App\Http\Controllers\Backend\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Redis;

class EmployeeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);
        return view('backend.store.employee.index',[
            'query_string'=>http_build_query($all)
        ]);
    }

    public function list(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('employee/list', $all);
        return $data;
    }

    public function edit(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('employee/get', $all);
        return view('backend.store.employee.edit',[
            'detail'=> $data['data']]);
    }

    public function add(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.store.employee.add');
    }

    public function update(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token'],$postData['_url']);
            return $this->curl('employee/update', $postData);
        }return $this->error();
    }

    public function bindAll(Request $request)
    {
        return $this->curl('employee/bindAll');
    }


}
