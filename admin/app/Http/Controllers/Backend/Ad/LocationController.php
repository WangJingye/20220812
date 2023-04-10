<?php

namespace App\Http\Controllers\Backend\Ad;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class LocationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.ad.location.index',[
            'query_string'=>http_build_query($all)
        ]);
    }

    public function list(Request $request)
    {
        $data = $this->curl('ad/loc/list', $request->all());
        return $data;
    }

    public function edit(Request $request)
    {
        $data = $this->curl('ad/loc/get', $request->all());
        $detail = $data['data'];

        return view('backend.ad.location.edit',[
            'detail'=> array_to_object($detail),
        ]);
    }

    public function add(Request $request)
    {
        return view('backend.ad.location.add');
    }

    public function insert(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('ad/loc/insert', $postData);

            return $response;
        }
    }

    public function update(Request $request)
    {
//        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token']);
            $editBack = $this->curl('ad/loc/update', $postData);

            return $editBack;
//        }
    }
}
