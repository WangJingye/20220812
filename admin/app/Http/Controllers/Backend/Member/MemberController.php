<?php

namespace App\Http\Controllers\Backend\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class MemberController extends Controller
{
    //
    public function list(Request $request)
    {
        $response = $this->curl('member/list', $request->all());
        $data['code'] = 0;
        $data['count'] = $response['data']['total'];
        $data['data'] = $response['data']['data'];
        foreach($data['data'] as &$v){
            $v['action'] = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="couponList">优惠券列表</a>';
        }
        return $data;
    }

    public function detail(Request $request)
    {
        $response = $this->curl('member/detail', request()->all());
        return view('backend.member.edit', $response);
    }

    public function destroy(Request $request)
    {
        $response = $this->curl('member/destroy', request()->all());

        return response()->json($response);
    }

    public function merge(Request $request)
    {
        return view('backend.member.merge');
    }

    public function getSlaveAndMasterMember(Request $request)
    {
        $response = $this->curl('member/getSlaveAndMasterMember', request()->all());
        $data = $response['data'] ?? [];
        $fields = $data['fields'] ?? [];
        $infos = $data['infos'] ?? [];

        return view('backend.member.smmember', ['fields' => $fields, 'infos' => $infos, 'master_id' => $request->master_id, 'slave_id' => $request->slave_id]);
    }

    public function mergeSlaveMemberIntoMasterMember(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('member/mergeSlaveMemberIntoMasterMember', $postData);
            return $response;
        }
    }

    public function import()
    {
        return view('backend.member.importCoupon');
    }

    public function importCoupon(Request $request)
    {
        try {
            $file = $_FILES;
            $excel_file_path = $file['file']['tmp_name'];
            if (!$excel_file_path) return [];
            $content = file_get_contents($excel_file_path);
            $content_data = explode("\r\n", $content);
            $content_data = array_filter($content_data);
            $data = [];
            foreach ($content_data as $k => $record) {
                if ($k == 0) continue;
                $fields = explode(',', $record);
                $data[] = $fields;
            }

//            if(!$data || !$method) return ['code'=>0];
            $response = $this->curl('member/importCoupon', ['data' => json_encode($data)]);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }

    }

    public function userAddress(Request $request)
    {
        $data = $request->all();
        $data['sn'] = env('APP_KEY');
        $response = $this->curl('member/innerGetUserAddress', $data);


        return response()->json($response);
    }


    public function getUserInfo(Request $request)
    {
        $data = $request->all();
        $response = $this->curl('member/getMemberInfo', $data);
        return response()->json($response);
    }

    public function exportMember(Request $request){
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('member/exportMember', $all);
        $list = $data['data'];
        $columns = $list[0];
        unset($list['0']);
        $result = [
            'columns' => $columns,
            'value' => array_values($list),
        ];
        return $this->success($result, 'success');
    }
}