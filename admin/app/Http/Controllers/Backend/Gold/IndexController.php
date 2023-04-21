<?php

namespace App\Http\Controllers\Backend\Gold;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class IndexController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        return view('backend.gold.index');
    }

    public function list(Request $request)
    {
        return $this->curl('goods/gold/list', request()->all());
    }

    public function changeStatus(Request $request)
    {
        return $this->curl('goods/gold/changeStatus', $request->all());
    }

    public function delete(Request $request)
    {
        return $this->curl('goods/gold/delete', $request->all());
    }

    public function add(Request $request)
    {
        return view('backend.gold.add');
    }

    public function insert(Request $request)
    {
        return $this->curl('goods/gold/add', $request->all());
    }

    public function userBalanceLog(Request $request)
    {
        return view('backend.gold.log');
    }

    public function getUserBalanceLogs(Request $request)
    {
        return $this->curl('member/user/getBalanceLogs', $request->all());
    }

    public function order(Request $request)
    {
        return view('backend.gold.order');
    }

    public function userBalanceList(Request $request)
    {
        return view('backend.gold.userBalance');
    }

    public function refund(Request $request)
    {
        return $this->curl('order/goldOrder/refund', $request->all());
    }

    public function getUserBalanceList(Request $request)
    {
        return $this->curl('member/user/getUserBalanceList', $request->all());
    }

    public function exportLog(Request $request)
    {
        $data = $this->curl('member/user/exportBalanceLogs', $request->all());
        $list = $data['data'];
        $columns = $list[0];
        unset($list['0']);
        $data = [
            'columns' => $columns,
            'value' => array_values($list),
        ];
        return $this->success($data, 'success');
    }

    public function invoice(Request $request)
    {
        return $this->curl('member/user/invoice', $request->all());
    }

}
