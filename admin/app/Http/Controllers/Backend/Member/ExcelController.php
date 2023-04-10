<?php

namespace App\Http\Controllers\Backend\Member;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Excel;

class ExcelController extends Controller
{
    // 数据导出
    public function export(Request $request)
	{
		$response = $this->curl('member/excel/list', request()->all());
        $cellData = $response['data'];
        return $cellData;

	}

}
