<?php

namespace App\Http\Controllers\Backend\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class SppController extends Controller
{
    public $ruleTypeMap = [
        '1' => '按品牌系列',
        '2' => '按用途',
        '3' => '按材质',
        '4' => '按指定款号',
        '5' => '全部',
    ];

    public $prodTypeList;
    public $prodUsage;
    public $prodBrandCollList;

    public function __construct()
    {
        parent::__construct();
        $this->prodTypeList = $this->curl('goods/common/getProdTypelist');
        $this->prodUsage = $this->curl('goods/common/getUsage');
        $this->prodBrandCollList = $this->curl('goods/common/getBrandColl');
    }

    public function index(Request $request)
    {
        return view('backend.config.spp.index');
    }

    public function add(Request $request)
    {
        $return = [];
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token'], $postData['file']);
            $addBack = $this->curl('goods/spp/add', $postData);

            return $addBack;
        }

        return view('backend.config.spp.add', ['ruleTypeMap' => $this->ruleTypeMap, 'prodTypeList' => array_to_object($this->prodTypeList), 'prodUsage' => array_to_object($this->prodUsage), 'prodBrandCollList' => array_to_object($this->prodBrandCollList)]);
    }

    public function get(Request $request)
    {
        $data = $this->curl('goods/spp/getSppRule', $request->all());
        $detail = $data['data'];

        return view('backend.config.spp.edit', ['detail' => array_to_object($detail), 'ruleTypeMap' => $this->ruleTypeMap, 'prodTypeList' => array_to_object($this->prodTypeList), 'prodUsage' => array_to_object($this->prodUsage), 'prodBrandCollList' => array_to_object($this->prodBrandCollList)]);
    }

    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token'], $postData['file']);
            $response = $this->curl('goods/spp/edit', $postData);

            return $response;
        }
    }

    public function del(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            $changeBack = $this->curl('goods/spp/del', $postData);

            return $changeBack;
        }
    }

    public function look(Request $request)
    {
        $data = $this->curl('goods/spp/getSppRule', $request->all());
        $detail = $data['data'];

        return view('backend.config.spp.look', ['detail' => array_to_object($detail), 'ruleTypeMap' => $this->ruleTypeMap, 'prodTypeList' => array_to_object($this->prodTypeList), 'prodUsage' => array_to_object($this->prodUsage), 'prodBrandCollList' => array_to_object($this->prodBrandCollList)]);
    }

    public function list(Request $request)
    {
        $response = $this->curl('goods/spp/list', request()->all());

        return $response['data'];
    }
}
