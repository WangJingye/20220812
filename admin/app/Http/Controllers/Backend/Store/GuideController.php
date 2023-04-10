<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/6
 * Time: 11:35
 */
namespace App\Http\Controllers\Backend\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Redis;


class GuideController extends Controller {

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 导购主页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $all = $request->all();
        $searchData = $this->curl('member/getSearchData');
        unset($all['_url']);
        return view('backend.store.guide.index',[
            'query_string'=>http_build_query($all),
            'searchData' => $searchData['data']
        ]);
    }

    /**
     * 导购主页数据
     * @param Request $request
     * @return mixed
     */
    public function list(Request $request) {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('order/guide/list', array_filter($all));
        $orders = array_get($data,'data.data');
        $total = array_get($data,'data.total');
        $store_list = $this->getStoreList();
        $employee_list = $this->getEmployeeList();
        $list = [];
        foreach($orders as $order){
            $list[] = [
                'order_sn'=>$order['order_sn'],
                'total_num'=>$order['total_num'],
                'status_name'=>$order['status_name'],
                'total_amount'=>$order['total_amount'],
                'store_name'=>array_get($store_list,$order['guide_id']),
                'guide_name'=>array_get($employee_list,$order['guide_id']),
                'created_at'=>$order['created_at'],
            ];
        }
        return [
            'list'=>$list,
            'total'=>$total,
        ];
    }

    /**
     * 导购详情页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function showInfo(Request $request) {
        $guide_id = $request->guide_id;
        if($guide_id) {
            $all = $request->all();
            unset($all['_url']);
            return view('backend.store.guide.showInfo',[
                'query_string'=>http_build_query($all),
            ]);
        }
    }

    /**
     * 导购详情页所需数据
     * @param Request $request
     * @return mixed
     */
    public function showInfoList(Request $request) {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('store/guideInfoPage', $all);
        return $data;
    }


    /**
     * 导购实时页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function realTime(Request $request) {
        $all = $request->all();
        if(!isset($all['guide_id']))
        {
            $all['guide_id'] = 0;
        }
        $orderList = $this->curl('order/index',$all);
        $searchData = $orderList['data'];
        $store_list = $this->getStoreList();
        $employee_list = $this->getEmployeeList();
        $searchData['stores'] = $store_list;
        $searchData['guides'] = $employee_list;
        unset($all['_url']);
        return view('backend.store.guide.realTime',[
            'query_string'=>http_build_query($all),
            'searchData' => $searchData
        ]);
    }
    /**
     * 导购实时页面所需数据
     * @param Request $request
     * @return mixed
     */
    public function realTimeList(Request $request) {
        $store_list = json_decode(Redis::get('store.list'),true);
        $all = $request->all();
        if(!isset($all['guide_id']))
        {
            $all['guide_id'] = 0;
        }
        unset($all['_url']);
        $data = $this->curl('order/index', $all);
        foreach ($data['data']['data'] as $key => $value) {
            $data['data']['data'][$key]['store_name'] = $store_list[$value['store_code']] ??   '';
            $data['data']['data'][$key]['guide_name'] = $store_list[$value['share_from']] ??   '';
        }
        $result = $data['data'];
        $result['code'] = $data['code'] == 1 ? 0 : 1 ;
        $result['message'] = $data['message'];
        //$data['data'];
        //$data = $this->curl('store/realTimeGuideInfo',$all);
        return json_encode($result);
    }



    public function getStoreFromCity(Request $request){
        $all = $request->all();
        $data = $this->curl('store/getStoreFromCity',$all);

        return $data;
    }

    public function dashBoardNeedData(Request $request)
    {
        $all = $request->all();
        $list = $this->curl('store/dashBoardNeedData',$all);

        return $list;
    }

    protected function getStoreList(){
//        try{
//            return array_get($this->curl('store/AllList'),'data')?:[];
//        }catch (\Exception $e){
//            return [];
//        }
        try{
            $list = array_get($this->curl('employee/allList'),'data');
            return $list?array_pluck($list,'store_name','sid'):[];
        }catch (\Exception $e){
            return [];
        }
    }

    protected function getEmployeeList(){
        try{
            $list = array_get($this->curl('employee/allList'),'data');
            return $list?array_pluck($list,'name','sid'):[];
        }catch (\Exception $e){
            return [];
        }
    }
}