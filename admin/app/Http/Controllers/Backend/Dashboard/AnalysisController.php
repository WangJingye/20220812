<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function getOrderReportData(Request $request)
    {
        $postData['viewType'] = $request->viewType;
        $postData['dateType'] = $request->dateType;
        return $this->curl('order/getOrderReportData', $postData);
    }
}