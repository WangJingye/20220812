<?php

namespace App\Http\Controllers\Api\Gold;

use App\Http\Controllers\Api\Controller;
use App\Model\Goods\Gold;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    protected $_model = Gold::class;

    /**
     * 储值卡列表，支持模糊查询.
     */
    public function list(Request $request)
    {
        $limit = request('limit', 10);
        $list = (new Gold())
            ->where('status', '!=', '0')
            ->where('gold_type', 1)
            ->orderBy('id', 'desc')->paginate($limit)->toArray();
        $return = [];
        $return['pageData'] = $list['data'];
        $return['count'] = $list['total'];
        return json_encode($return);
    }

    /**
     * 修改储值卡上下架状态.
     */
    public function changeStatus(Request $request)
    {
        $gold = Gold::query()->where('id', $request->get('id'))->first();
        $gold['status'] = $request->get('status');
        $gold->save();
        return $this->success([]);
    }

    /**
     * 删除储值卡
     */
    public function delete(Request $request)
    {
        $gold = Gold::query()->where('id', $request->get('id'))->first();
        $gold['status'] = 0;
        $gold->save();
        return $this->success([]);
    }

    /**
     * 删除储值卡
     */
    public function add(Request $request)
    {
        $data = $request->all();
        if (empty($data['gold_type']) || $data['gold_type'] != 2) {
            $gold = Gold::query()
                ->where('gold_name', $data['gold_name'])
                ->where('status', '!=', '0')
                ->first();
            if (!empty($gold)) {
                return $this->error(0, '储值卡名称重复，不能添加');
            }
        }
        $gold = new Gold($data);
        $gold['face_value'] = round($gold['price'] * $gold['rate'], 2);
        if (!empty($data['gold_type']) && $data['gold_type'] == 2) {
            $gold['face_value'] = $data['face_value'];
        }
        $gold['rate'] = round($gold['rate'], 2);
        $gold->save();
        return $this->success($gold);
    }

    public function detail(Request $request)
    {
        $data = $request->all();
        $gold = Gold::query()->where('id', $data['id'])->first();
        return $this->success($gold);
    }
}
