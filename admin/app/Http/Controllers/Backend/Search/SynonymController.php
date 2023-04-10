<?php

namespace App\Http\Controllers\Backend\Search;

use App\Model\Help;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class SynonymController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.search.synonym.list',[
            'query_string'=>http_build_query($all)
        ]);
    }

    public function export(Request $request){
//        $brand = Help::getBrandCode();
        $response = $this->curl('goods/search/getAllSynonym', $request->all());
        $data = $response['data']??[];
        $content = '';
        foreach($data as $one){
            $content .= $one['word'].'=>'.$one['convert_word'].PHP_EOL;
        }

        Header( "Content-type:   application/octet-stream ");
        Header( "Accept-Ranges:   bytes ");
        header( "Content-Disposition:   attachment;   filename=syno_dlc.txt ");
        header( "Expires:   0 ");
        header( "Cache-Control:   must-revalidate,   post-check=0,   pre-check=0 ");
        header( "Pragma:   public ");

        echo $content;
        exit;
    }

    public function delSynonym(Request $request)
    {
        $response = $this->curl('goods/search/delSynonym', $request->all());

        return $response;
    }

    public function add(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.search.synonym.add',[
            'query_string'=>http_build_query($all)
        ]);
    }

    public function edit(Request $request)
    {
        $response = $this->curl('goods/search/getSynonymInfo', request()->all());

        return view('backend.search.synonym.edit',[
            'detail'=>array_to_object($response['data'])
        ]);
    }

    public function updateSynonym(Request $request)
    {
        $response = $this->curl('goods/search/updateSynonym', request()->all());

        return $response;
    }

    public function addSynonym(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('goods/search/addSynonym', $postData);

            return $response;
        }
    }

    public function getSynonymList()
    {
        $response = $this->curl('goods/search/synonymList', request()->all());
        return $response['data'];
    }


    public function getSynonym()
    {
        $response = $this->curl('goods/search/getSynonymInfo', request()->all());


        return view('backend.search.synonym.edit',[
            'detail'=>array_to_object($response['data'])
        ]);
    }

}
