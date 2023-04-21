<?php

namespace App\Http\Controllers\Outward\Gold;

use App\Http\Controllers\Api\Controller;
use App\Model\Goods\Gold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    /**
     * 储值卡列表，支持模糊查询.
     */
    public function getList(Request $request)
    {
        $list = Gold::query()
            ->where('status', '1')
            ->whereRaw('(link_start_time is null or link_start_time <= "'.date('Y-m-d H:i:s').'")')
            ->whereRaw('(link_end_time is null or link_end_time >= "'.date('Y-m-d H:i:s').'")')
            ->orderBy('price')
            ->get()->toArray();
        return $this->success($list);
    }

    /**
     * 储值卡须知
     * @param Request $request
     */
    public function goldGuide(Request $request)
    {
        $text = DB::table('tb_config')->where('name', 'gold_guide')->pluck('value');
        return $this->success(['guide' => $text], 'success');
    }
}
