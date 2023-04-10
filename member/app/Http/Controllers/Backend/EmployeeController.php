<?php


namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\SaList;
use Illuminate\Http\Request;
use Validator;

class EmployeeController extends Controller
{

    /**
     * 列表，支持模糊查询.
     */
    public function list(Request $request)
    {

        $limit = $request->limit ?: 10;

        $emply = new SaList;
        if ($request->name) {
            $emply = $emply->where('name', 'like', $request->name . '%');
        }
        if ($request->store_name) {
            $emply = $emply->where('store_name', 'like', $request->store_name . '%');
        }
        if ($request->role_name) {
            $emply = $emply->where('role_name', 'like', $request->role_name . '%');
        }

        $data = $emply->orderBy('id','desc')->paginate($limit)->toArray();

        $return = [];
        if ($data) {
            $return['pageData'] = $data['data'];
            $return['count'] = $data['total'];

        }
        return $this->success('success', $return);

    }

    /**
     * 获取
     */
    public function getEmployee(Request $request)
    {
        if ($request->id) {
            $info = SaList::where('id', $request->id)->first();
        }
        return $this->success('success', $info??[]);
    }

    /**
     * 编辑
     */
    public function updateEmployee(Request $request)
    {
        try{
            $skuIdx = $request->id;
            $updateData = $request->all();
            unset($updateData['id'], $updateData['_url']);
            SaList::query()->updateOrCreate(
                ['id' => $skuIdx],
                $updateData
            );
            return $this->success();
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }


    /**
     * 列表，支持模糊查询.
     */
    public function AllList(Request $request)
    {
        $emply = new SaList;
        if ($request->name) {
            $emply = $emply->where('name', 'like', $request->name . '%');
        }
        if ($request->store_name) {
            $emply = $emply->where('store_name', 'like', $request->store_name . '%');
        }
        if ($request->role_name) {
            $emply = $emply->where('role_name', 'like', $request->role_name . '%');
        }

        $data = $emply->get()->toArray();
        $return = [];
        if ($data) {
            $return=  $data;
        }
        return $this->success('success', $return);

    }

    /**
     * 实时查询导购数据
     * 可筛选导购id
     * 可调整时间段（一天之内）
     */
    public function realTimeGuideInfo(Request $request)
    {

        //只可查询当前自然日内的数据
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        //判断日期是否是今天
        if (!EmployeeService::dateIsToday($startTime) &&
            !EmployeeService::dateIsToday($endTime) &&
            $endTime <= $startTime) {
            return $this->error('参数不合法!');
        }


        //如果只选择了导购，则查询item表
        if ($request->guide_id) {
            if ($request->guide_id) $where[] = ['guide_id', '=', $request->guide_id];
            $guideInfo = OrderGoodsService::getGoodsFromGuide($where, $startTime, $endTime);
        } else {
            $where = [];
            $where[] = ['transaction_date', '=', date('Y-m-d', strtotime($startTime))];
            if ($request->store_id) {
                //根据门店ID获得门店code
                $store_code = StoresService::getCodeFromId($request->store_id);
                $where[] = ['store_code', '=', $store_code[0]['store_code']];
            }
            if ($request->city) $where[] = ['city', '=', $request->city];
            if ($request->province) $where[] = ['province', '=', $request->province];

            $guideInfo = OrdersService::getOrdersInfo($where, ['id']);
            $allGoods = OrderGoodsService::ordersGoodsGuide(array_column($guideInfo, 'id'), [], 10);

            $guideInfo = OrderGoodsService::handleItemData($allGoods);
        }
        $return = [];
        if ($guideInfo) {
            $return['pageData'] = $guideInfo['data'];
            $return['over_view'] = $guideInfo['over_view'];
            $return['count'] = $guideInfo['total'];
        }
        return $this->success('success', $return);

    }

    public function bindAll(Request $request){
        $result = SaList::bindAll();
        if($result===true){
            return $this->success('success');
        }return $this->error($result);
    }



}