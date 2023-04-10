<?php

namespace App\Http\Controllers\Backend\Oms;

use App\Http\Controllers\Backend\Controller;
use http\QueryString;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class OrderManagerController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {

        $all = $request->all();
        if(!isset($all['status'])){
            $all['status'] = 'pending-refunded';
        }
        unset($all['_url']);

        $data = $this->curl('order/index', $all);
        $list = $data['data'];
        $action = $this;
        return view('backend.oms.manager.index', compact('list','action'));
    }

    public function list(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);

        $data = $this->curl('order/index', $all);
        $list = $data['data'];
        $action = $this;
        return view('backend.oms.manager.index', compact('list','action'));
    }



    public function edit(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('order/get', $all);

        $detail = $data['data'];

        return view('backend.oms.manager.edit', [
            'order' => $detail,
        ]);
    }


    public function update(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token'], $postData['_url']);
            $editBack = $this->curl('order/update', $postData);
            return $editBack;
        }
    }



    public function updateOrderStatus(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);

        $data = $this->curl('order/status/update', $all);

        return $data;
    }

    public function getStatusFilter($status)
    {
        switch ($status) {
            //所有
            case 'all':
                return url('admin/oms/order/manager/index').toQuery(array_merge(request()->all(),['status'=>'all','page'=>1]));
                break;
            //待付款
            case 'pending':
                return url('admin/oms/order/manager/index').toQuery(array_merge(request()->all(),['status'=>'pending','page'=>1]));
                break;
            //已支付
            case 'finished':
                return url('admin/oms/order/manager/index').toQuery(array_merge(request()->all(),['status'=>'finished','page'=>1]));
                break;
            //待发货
            case 'pending-shiped':
                return url('admin/oms/order/manager/index').toQuery(array_merge(request()->all(),['status'=>'pending-shiped','page'=>1]));
                break;
            //已发货
            case 'finished-shiped':
                return url('admin/oms/order/manager/index').toQuery(array_merge(request()->all(),['status'=>'finished-shiped','page'=>1]));
                break;
            //售后
            case 'after-sales':
                return url('admin/oms/order/manager/index').toQuery(array_merge(request()->all(),['status'=>'after-sales','page'=>1]));
                break;
            //已关闭
            case 'cancel':
                return url('admin/oms/order/manager/index').toQuery(array_merge(request()->all(),['status'=>'cancel','page'=>1]));
                break;
            case 'pending-refunded':
                return url('admin/oms/order/manager/index').toQuery(array_merge(request()->all(),['status'=>'pending-refunded','page'=>1]));
                break;
            case 'refunded':
                return url('admin/oms/order/manager/index').toQuery(array_merge(request()->all(),['status'=>'refunded','page'=>1]));
                break;


        }
    }

    function getActionStatus($status){
        $currentStatus = request('status','pending-refunded');
        if($currentStatus ==$status){
            return 'layui-btn-normal';
        }else{
            return 'layui-btn-primary';
        }
    }


}