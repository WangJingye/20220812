<?php

namespace App\Http\Controllers\Backend\Search;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class RedirectController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.search.redirect.list',[
            'query_string'=>http_build_query($all)
        ]);
    }

    public function delRedirect(Request $request)
    {
        $response = $this->curl('goods/search/delRedirect', $request->all());

        return $response;
    }

    public function add(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.search.redirect.add',[
            'query_string'=>http_build_query($all)
        ]);
    }

    public function edit(Request $request)
    {
        $response = $this->curl('goods/search/getRedirectInfo', request()->all());

        return view('backend.search.redirect.edit',[
            'detail'=>array_to_object($response['data'])
        ]);
    }

    public function updateRedirect(Request $request)
    {
        $response = $this->curl('goods/search/updateRedirect', request()->all());

        return $response;
    }

    public function addRedirect(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('goods/search/addRedirect', $postData);

            return $response;
        }
    }

    public function getRedirectList()
    {
        $response = $this->curl('goods/search/redirectList', request()->all());
        return $response['data'];
    }


    public function getRedirect()
    {
        $response = $this->curl('goods/search/getRedirectInfo', request()->all());


        return view('backend.search.redirect.edit',[
            'detail'=>array_to_object($response['data'])
        ]);
    }

}
