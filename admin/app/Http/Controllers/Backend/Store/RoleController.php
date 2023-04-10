<?php

namespace App\Http\Controllers\Backend\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class RoleController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);
        return view('backend.store.role.index',[
            'query_string'=>http_build_query($all)
        ]);
    }

    public function list(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('role/list', $all);

        return $data;
    }

    public function edit(Request $request)
    {

        $data = $this->curl('role/get', $request->all());
        $detail = $data['data'];
        return view('backend.store.role.edit',[
            'detail'=> array_to_object($detail),
        ]);
    }

    public function add(Request $request)
    {
        return view('backend.store.role.add');
    }

    public function insert(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('role/insert', $postData);

            return $response;
        }
    }

    public function update(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token'],$postData['_url']);
            $editBack = $this->curl('role/update', $postData);
            return $editBack;
        }
    }
}
