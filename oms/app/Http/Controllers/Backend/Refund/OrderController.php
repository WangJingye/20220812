<?php namespace App\Http\Controllers\Backend\Refund;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Refund\OmsOrderRefundMain;

class OrderController extends Controller
{
    /**
     * 获取列表
     * @param Request $request
     * @return array
     */
    public function list(Request $request){
        $status = $request->get('status','');

        $list = OmsOrderRefundMain::list($status);
        print_r($list);exit;
        return $this->success(compact('list','total','states'));
    }


}
