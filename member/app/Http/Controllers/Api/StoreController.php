<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Users;
/*use App\Model\GoodsSaleCount;
use App\Model\OmsOrderMain;
use App\Model\TimingGuideRanking;
use App\Service\Store\StoreService;
use App\Service\OrdersService;
use App\Service\OrderGoodsService;
use App\Service\EmployeeService;
use App\Service\GoodsSaleCountService;*/
use App\Model\Store;
//use App\Model\Orders;
//use App\Service\TimingGuideRankingService;
use Illuminate\Http\Request;
use Validator;
use App\Tools\Tools;

class StoreController extends Controller
{


    public function getAllStoreData(Request $request){
        $limit = $request->limit ?? 1000;
        $store = new Store;
        if ($request->store_name) {
            $store = $store->where('store_name', 'like', $request->store_name . '%');
        }

        $data = $store->paginate($limit)->toArray();

        $return = [];
        if ($data) {
            $return['pageData'] = $data['data'];
            $return['count'] = $data['total'];
        }
        return $this->success('success', $return);
    }

    /**
     * 列表，支持模糊查询.
     */
    public function list(Request $request)
    {
        $limit = $request->limit ?? 10;

        $store = new Store;
        if ($request->store_name) {
            $store = $store->where('store_name', 'like', $request->store_name . '%');
        }
        $data = $store->orderby('id','desc')->paginate($limit)->toArray();

        $return = [];
        if ($data) {
            $return['pageData'] = $data['data'];
            $return['count'] = $data['total'];
        }
        return $this->success('success', $return);
    }

    /**
     * 导购，门店，城市每天销量榜单
     * todo:利用导购销量表内数据做运算即可。
     * todo:导购，查询金额最高的倒序
     * todo:门店，根据门店分组，计算金额倒序
     * todo:城市，根据城市分组，计算金额倒序
     * @param int $type all=全部
     */
    public function guideStoreCityTop($type = 0)
    {
        $time = date('Y-m-d', strtotime('-1 day'));

        $data = [];
        $data['guides'] = $this->guideRankingList($time);
        $data['stores'] = $this->storeRankingList($time);
        $data['citys'] = $this->cityRankingList($time);

        return $data;
    }


    /**
     * 门店排行top10
     * 导购排行榜已计算出，根据导购所在的门店城市计算门店城市top
     */
    public function storeRankingList($time = '')
    {
        if ($time == '') {
            return;
        }
        $data = TimingGuideRankingService::getStoreOrCityFromTime($time);
        return $data;
    }

    /**
     * 城市排行top10
     * 导购排行榜已计算出，根据导购所在的门店城市计算门店城市top
     */
    public function cityRankingList($time = '')
    {
        if ($time == '') {
            return;
        }
        $data = TimingGuideRankingService::getStoreOrCityFromTime($time, 'city_name');
        return $data;
    }

    /**
     * 导购排行top10
     */
    public function guideRankingList($time = '')
    {

        if ($time == '') {
            return;
        }
        $data = TimingGuideRankingService::getGuideFromTime($time);
        return $data;
    }


    /**
     * 后台导购页面定时脚本
     * 查询出昨日所有订单
     * 订单按照导购group
     */
    public function guideInfoTiming()
    {
        //昨日订单
        $yesterdayOrders = OrdersService::getYesterdayOrders(['id']);
        $ordersId = array_column($yesterdayOrders, 'id');
        //查询出订单内所有商品
        $allGoodsGuideWaitHandle = OrderGoodsService::ordersGoodsGuide($ordersId, ['id', 'order_main_id', 'sku', 'qty', 'product_amount_total', 'guide_id', 'name']);
        //处理数组将相同导购的数据合并在一起,组装入库数据
        EmployeeService::handleGuideTimingData($allGoodsGuideWaitHandle);
    }

    /**
     * 导购数据主页面
     * 查询全部数据倒序展示
     * 城市，门店，导购筛选条件
     */
    public function allGuideInfoPage(Request $request)
    {
        //筛选条件
        $where = [];
        if ($request->guide_id) {
            $where[] = ['guide_id', '=', $request->guide_id];
        }
        if ($request->store_id) {
            $where[] = ['store_id', '=', $request->store_id];
        }
        if ($request->city_name) {
            $where[] = ['city_name', '=', $request->city_name];
        }
        if ($request->province) {
            $where[] = ['address', '=', $request->province];
        }

        $time = 0;
        if ($request->start_time && $request->end_time) {
            $where[] = ['time', '>=', $request->start_time];
            $where[] = ['time', '<=', $request->end_time];
            $time = $request->start_time . '__' . $request->end_time;
        }

        $limit = $request->limit ? $request->limit : 10;
        $service = new TimingGuideRankingService();
        $list = $service->getList($where, $limit, $time);

        $return = [];
        if ($list) {
            $return['pageData'] = $list['data'];
            $return['count'] = $list['total'];
        }
        return $this->success('success', $return);

    }

    /**
     * 获得城市，门店，导购数据
     */
    public function getSearchData(Request $request)
    {
        $info = [];
        if ($request->type == 'guide') {
            $info['guides'] = EmployeeService::getGuide();
        } else {
            $info['citys'] = StoreService::getCity();
            $info['stores'] = StoreService::getStore();
            $info['province'] = StoreService::getProvince();
            $info['guides'] = EmployeeService::getGuide();
        }

        return $this->success('success', $info);
    }

    /**
     * 导购数据详情页
     * 导购id
     */
    public function guideInfoPage(Request $request)
    {
        $guide_id = $request->guide_id;
        $limit = $request->limit;
        $time = $request->time ?? 0;

        if (strpos($time, '__') !== false) {
            $time = explode('__', $time);
            $start = $time[0];
            $end = date('Y-m-d', strtotime($time[1]) + 86400);
        } else {
            $start = $time;
            $end = date('Y-m-d', strtotime($time) + 86400);
        }

        //根据导购id查询该导购推销出的所有产品
        $where = [
            ['guide_id', '=', $guide_id]
        ];
        $allGoods = OrderGoodsService::getGoodsFromGuide($where, $start, $end, $limit);

        $return = [];
        if ($allGoods) {
            $return['pageData'] = $allGoods['data'];
            $return['over_view'] = $allGoods['over_view'];
            $return['count'] = $allGoods['total'];
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
                $store_code = StoreService::getCodeFromId($request->store_id);
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

    /**
     * 分类销量
     */
    public function typeSaleVolumeTop()
    {
        $time = date('Y-m-d', strtotime('-1 day'));
        $types = GoodsSaleCountService::typeSaleCount($time);
        $model = new GoodsSaleCount();
        $model->addAll($types);
    }


    /**
     * 来源渠道销量
     */
    public function channelSaleVolumeTop()
    {
        //昨日所有订单
        $orders = OrdersService::getYesterdayOrders(['channel']);
        $orders = array_count_values(array_column($orders, 'channel'));

        $data = [];
        foreach (OmsOrderMain::ORDER_CHANNEL as $key => $val) {
            $data[] = [
                'qty' => $orders[$key],
                'name' => $val,
                'time' => date('Y-m-d', strtotime('-1 day')),
                'type' => GoodsSaleCount::SALE_COUNT_CHANNEL,
                'display_type' => $key
            ];
        }

        $model = new GoodsSaleCount();
        $model->addAll($data);
    }


    /**
     * 导购招募会员数
     */
    public function guideRegisterUserCount()
    {
        $start = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $end = date('Y-m-d 00:00:00');
        $model = new Users();
        $guideModel = new TimingGuideRanking();
        $where = [
            ['created_at', '>=', $start],
            ['created_at', '<', $end],
            ['guide_id', '<>', 0]
        ];
        //查询昨日注册用户的招募导购id
        $guides = $model->where($where)->get(['guide_id'])->toArray();
        $guides = array_count_values(array_column($guides, 'guide_id'));
        foreach ($guides as $key => $val) {
            $id = $guideModel->where([['guide_id', '=', $key], ['time', '=', $start]])->get(['id'])->toArray();
            if ($id) {
                $guideModel->where('id', '=', $id)->update(['reg_user_count' => $id[0]['id']]);
            }
        }
    }


    /**
     * 根据城市获得该城市下的门店
     * 根据门店获得导购
     * @param Request $request
     * @return mixed
     */
    public function getStoreFromCity(Request $request)
    {
        $all = $request->all();
        $type = array_keys($all);
        $where = [];
        switch ($type[0]) {
            case 'city_name':
                if ($all['city_name']) $where[] = ['city', '=', $all['city_name']];
                $data = StoreService::getStore($where);
                break;
            case 'store_id' :
                if ($all['store_id']) $where[] = ['store_id', '=', $all['store_id']];
                $data = EmployeeService::getGuide($where);
                break;
            case 'province' :
                if ($all['province']) $where[] = ['province', '=', $all['province']];
                $data = StoreService::getCity($where);
                break;

            default :
                $data = [];
        }
        return $this->success('success', $data);

    }


    /**
     * 排行数据
     * type    1=一个月 2=七天 3=昨天
     * @param Request $request
     * @return mixed
     */
    public function dashBoardNeedData(Request $request)
    {
        $type = $request->type ?? 0;

        if ($type == 0) {
            return $this->error('请求未携带TYPE!');
        }

        $end = date('Y-m-d');
        switch ($type) {
            case TimingGuideRanking::RANKING_TIME_TYPE_MONTH :
                $start = date('Y-m-d', strtotime('-1 month'));
                break;
            case TimingGuideRanking::RANKING_TIME_TYPE_WEEK :
                $start = date('Y-m-d', strtotime('-1 week'));
                break;
            case TimingGuideRanking::RANKING_TIME_TYPE_YESTERDAY :
                $start = date('Y-m-d', strtotime('-1 day'));
                break;
            default :
                $start = $end = '';
        }

        //商品排行
        $goodsList = TimingGuideRankingService::getDashBoardDataGoods($start, $end, $type);
        //导购排行
        $guideList = TimingGuideRankingService::getDashBoardDataGuide($start, $end, $type);
        //门店排行
        $storeList = TimingGuideRankingService::getDashBoardDataStore($start, $end);
        //城市排行
        $cityList = TimingGuideRankingService::getDashBoardDataCity($start, $end);
        //分类排行
        $typeList = TimingGuideRankingService::getDashBoardDataType($start, $end, $type);

        $list = [
            'goodsListTop' => Tools::formatNumberInArray($goodsList, ['amount']),
            'guidesListTop' => Tools::formatNumberInArray($guideList, ['amount']),
            'storesListTop' => Tools::formatNumberInArray($storeList, ['amount']),
            'citysListTop' => Tools::formatNumberInArray($cityList, ['amount']),
            'typesListTop' => $typeList
        ];

        return $this->success('success', $list);
    }


}