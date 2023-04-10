<?php

namespace App\Http\Controllers\Backend\Search;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class BlacklistController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.search.blacklist',[
            'query_string'=>http_build_query($all)
        ]);
    }

    public function delBlackList(Request $request)
    {
        $response = $this->curl('goods/search/delBlackList', $request->all());

        return $response;
    }

    public function addBlackList(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('goods/search/addBlackList', $postData);

            return $response;
        }
    }


    public function getBlackList()
    {
        $response = $this->curl('goods/search/blacklist', request()->all());

        return $response['data'];
    }

}
