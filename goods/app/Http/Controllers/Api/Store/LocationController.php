<?php
/**
 * Created by PhpStorm.
 * User: Jack.Xu1
 * Date: 2019/11/15
 * Time: 15:41
 */

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Api\Controller;
use App\Model\Goods\ProductHelp;
use PhpParser\Node\Expr\Array_;
use Illuminate\Support\Facades\Redis;
use Validator;
use App\Http\Helpers\Api\GoodsCurlLog;


/**
 * 腾讯位置服务 api document
 * https://lbs.qq.com/webservice_v1/index.html
 *
 *     $storeObj = [
 * 'name'=>'周生生分店',
 * 'distance'=>'2.9',
 * 'address'=>[
 * 'description'=>'江苏路东方街9号',
 * 'city'=>'南京市',
 * 'state'=>'江苏省',
 * ],
 * 'serviceProvision'=>[
 * 'operatingHour'=>'10:00;22:30;週五至週六","10:00;22:00;週日至週四',
 * 'product'=>['珠宝", "钻石私人导赏", "预约订制钻戒服务", "足金饰品易货服务'],
 * ],
 * 'tel'=>'13300001111',
 * 'latitude'=>'31.25956',
 * 'longitude'=>'121.52609'
 * ];
 *
 */
class LocationController extends Controller
{

    const  KEY = 'NI6BZ-7RDWP-QBSDW-VXYUG-MDDIK-YAFBP';

    function storeInventoryList()
    {

        $pageSize = 6;
        $latitude = request('latitude');
        $longitude = request('longitude');
        $state = request('state');
        $city = request('city');
        $sku = request('sku');
        $filter = request('filter', '[]');
        $filter = json_decode($filter);
        $curPage = request('curPage', 1);
        $log_frag = "_" . env("MODULE_NAME") . "_" . gethostname() . "_" . date("Y-m-d");
        error_log(print_r([
            'time' => date('Y-m-d H:i:s'),
            'params' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'state' => $state,
                'city' => $city,
                'filter' => $filter,
                'curPage' => $curPage
            ],
        ], true), 3, '../storage/logs/storeList' . $log_frag . '.Log');

        //$latitude=request('latitude');
        //$longitude= request('longitude');
        // $city='北京市';
        //$city='上海市';
        //$city='浦东新区';
        //$filter=['钟表','分店取货'];

        $STORE_LIST = Redis::get('CSS_$STORE_LIST');
        $STORE_LIST = json_decode($STORE_LIST, true);
        $STORE_ID = Redis::get('CSS_$STORE_ID');
        //店铺id映射
        $STORE_ID = json_decode($STORE_ID, true);
        //所有店铺列表
        $allStoreList = $STORE_LIST['storeList'];
        $currentCity = $this->_getCity($latitude, $longitude);

        unset($allStoreList[747]);

        //城市店铺筛选
        if ($currentCity && in_array($currentCity, array_keys($STORE_ID['city']))) {


            //过滤城市
            if ($city) {
                $cityStoreId = $STORE_ID['city'][$city];
                //当前城市
            } else {
                $cityStoreId = $STORE_ID['city'][$currentCity];
            }
            $storeList = array_intersect_key($allStoreList, $cityStoreId);
        } else {
            //过滤城市
            if ($city) {
                $cityStoreId = $STORE_ID['city'][$city];
                $storeList = array_intersect_key($allStoreList, $cityStoreId);
            } else {
                //所有店铺
                $storeList = $allStoreList;
            }


        }


        //服务筛选
        $serviceIDS = [];

        if (count($filter) > 0) {
            array_map(function ($key) use (&$serviceIDS, $STORE_ID) {
                $serviceIDS = array_merge($serviceIDS, $STORE_ID['services'][$key]);
            }, $filter);
        }

        if ($serviceIDS) {
            $storeList = array_filter(array_values(array_intersect_key($storeList, array_flip($serviceIDS))));
        }


        //距离排序
        $can = $this->canDistance($currentCity, $city, $STORE_LIST['cityTree']);

        if ($can) {
            //计算距离
            $from = $latitude . ',' . $longitude;
            $to = [];
            array_map(function ($item) use (&$to) {
                $to[] = $item['latitude'] . "," . $item['longitude'];
            }, $storeList);

            // dump($storeList);
            // dump($to);
            // unset($to[18]);
            $distance = $this->_distance($from, $to);

            if ($distance['status'] === 0) {
                $distanceElements = $distance['result']['elements'];

                $i = 0;
                $key_arrays = [];
                foreach ($storeList as &$store) {
                    $store['distance'] = $distanceElements[$i]['distance'];
                    $key_arrays[] = $store['distance'];
                    $i++;
                }
                // dd($key_arrays,count($storeList));

                array_multisort($key_arrays, SORT_ASC, SORT_NUMERIC, $storeList);

                foreach ($storeList as &$store) {
                    $store['distance'] = round(($store['distance'] / 1000), 2);
                }


            }


        }

        if ($sku) {
            $skuStore = [];
            $deSku = json_decode($sku, true);
            $storeIdArr = (new ProductHelp())->getDoorsBySku($deSku);
            foreach ($storeList as $store) {
                if (in_array($store['id'], $storeIdArr)) {
                    $skuStore[] = $store;
                }
            }
        } else {
            $skuStore = $storeList;
        }


        return $this->storePageData($skuStore, $curPage, $pageSize);
    }

    function storeList()
    {

        $pageSize = 6;
        $latitude = request('latitude');
        $longitude = request('longitude');
        $state = request('state');
        $city = request('city');
        $sku = request('sku');
        $filter = request('filter', '[]');
        $filter = json_decode($filter);
        $curPage = request('curPage', "");
        $log_frag = "_" . env("MODULE_NAME") . "_" . gethostname() . "_" . date("Y-m-d");
        error_log(print_r([
            'time' => date('Y-m-d H:i:s'),
            'params' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'state' => $state,
                'city' => $city,
                'filter' => $filter,
                'curPage' => $curPage
            ],
        ], true), 3, '../storage/logs/storeList' . $log_frag . '.Log');

        //$latitude=request('latitude');
        //$longitude= request('longitude');
        // $city='北京市';
        //$city='上海市';
        //$city='浦东新区';
        //$filter=['钟表','分店取货'];

        $STORE_LIST = Redis::get('CSS_$STORE_LIST');
        $STORE_LIST = json_decode($STORE_LIST, true);
        $STORE_ID = Redis::get('CSS_$STORE_ID');
        //店铺id映射
        $STORE_ID = json_decode($STORE_ID, true);
        //所有店铺列表
        $allStoreList = $STORE_LIST['storeList'];
        $currentCity = $this->_getCity($latitude, $longitude);

        unset($allStoreList[747]);

        //城市店铺筛选
        if ($currentCity && in_array($currentCity, array_keys($STORE_ID['city']))) {


            //过滤城市
            if ($city) {
                $cityStoreId = $STORE_ID['city'][$city];
                //当前城市
            } else {
                $cityStoreId = $STORE_ID['city'][$currentCity];
            }
            $storeList = array_intersect_key($allStoreList, $cityStoreId);
        } else {
            //过滤城市
            if ($city) {
                $cityStoreId = $STORE_ID['city'][$city];
                $storeList = array_intersect_key($allStoreList, $cityStoreId);
            } else {
                //所有店铺
                $storeList = $allStoreList;
            }


        }


        //服务筛选
        $serviceIDS = [];

        if (count($filter) > 0) {
            array_map(function ($key) use (&$serviceIDS, $STORE_ID) {
                $serviceIDS = array_merge($serviceIDS, $STORE_ID['services'][$key]);
            }, $filter);
        }

        if ($serviceIDS) {
            $storeList = array_filter(array_values(array_intersect_key($storeList, array_flip($serviceIDS))));
        }


        //距离排序
        $can = $this->canDistance($currentCity, $city, $STORE_LIST['cityTree']);

        if ($can) {
            //计算距离
            $from = $latitude . ',' . $longitude;
            $to = [];
            array_map(function ($item) use (&$to) {
                $to[] = $item['latitude'] . "," . $item['longitude'];
            }, $storeList);

            // dump($storeList);
            // dump($to);
            // unset($to[18]);
            $distance = $this->_distance($from, $to);

            if ($distance['status'] === 0) {
                $distanceElements = $distance['result']['elements'];

                $i = 0;
                $key_arrays = [];
                foreach ($storeList as &$store) {
                    $store['distance'] = $distanceElements[$i]['distance'];
                    $key_arrays[] = $store['distance'];
                    $i++;
                }
                // dd($key_arrays,count($storeList));

                array_multisort($key_arrays, SORT_ASC, SORT_NUMERIC, $storeList);

                foreach ($storeList as &$store) {
                    $store['distance'] = round(($store['distance'] / 1000), 2);
                }


            }


        }

//        error_log(print_r([
//            'time' => date('Y-m-d H:i:s'),
//            'response' => $storeList,
//        ], true), 3, '../storage/logs/storeList.log');
        if($curPage ==''){
            $curPage= 1;
            $pageSize='1000000';
        }

        return $this->storePageData($storeList, $curPage, $pageSize);


    }

    /**
     * 是否需要计算距离
     * @param $currentCity
     * @param $city
     * @return bool
     */
    function canDistance($currentCity, $city, $cityTree)
    {
        if (empty($city)) return true;
        if ($currentCity == $city) {
            return true;
        }

        $directlyCityArr = ['上海市', '北京市', '天津市', '重庆市'];

        if (in_array($currentCity, $directlyCityArr)) {
            foreach ($directlyCityArr as $directlyCity) {
                $directly = strtr($directlyCity, ['市' => '']);
                if (in_array($currentCity, $cityTree[$directly]) && in_array($city, $cityTree[$directly])) {
                    return true;
                }

            }
        }

        return false;
    }


    function storePageData($storeList, $curPage, $pageSize)
    {
        $totalCount = count($storeList);
        $totalPage = ceil(count($storeList) / $pageSize);
        $offset = ($curPage - 1) * $pageSize;
        $storeList = array_slice($storeList, $offset, $pageSize);

        $storeList = array_map(function ($store) {
            $store['latitude'] = (float)$store['latitude'];
            $store['longitude'] = (float)$store['longitude'];
            return $store;
        }, $storeList);

        $storeList = array_values($storeList);


        $data = [
            'list' => $storeList,
            'totalPage' => $totalPage,
            'totalCount' => $totalCount,
            'curPage' => $curPage,
            'pageSize' => $pageSize
        ];


        return $this->success($data);
    }


    /**
     * 获取当前城市店铺列表
     */
    protected function _currentCityStoreList($latitude, $longitude, $curPage, $pageSize)
    {

        $STORE_LIST = Redis::get('CSS_$STORE_LIST');
        $STORE_LIST = json_decode($STORE_LIST, true);
        $storeList = $STORE_LIST['storeList'];


        if ($latitude && $longitude) {

            $STORE_ID = Redis::get('CSS_$STORE_ID');
            $STORE_ID = json_decode($STORE_ID, true);
            $currentCity = $this->_getCity($latitude, $longitude);
            $cityID = $STORE_ID['city'][$currentCity];
            $storeList = array_filter(array_values(array_intersect_key($storeList, $cityID)));

            //计算距离
            $from = $latitude . ',' . $longitude;
            $to = [];

            array_map(function ($item) use (&$to) {
                $to[] = $item['latitude'] . "," . $item['longitude'];
            }, $storeList);
            $distance = $this->_distance($from, $to);
            if ($distance['status'] === 0) {
                $distanceElements = $distance['result']['elements'];
            } else {
                return $this->error(0, '调用距离接口错误或者没有店铺数据');
            }


            for ($i = 0; $i < count($storeList); $i++) {
                $storeList[$i]['distance'] = round(($distanceElements[$i]['distance'] / 1000), 2);
            }
        }


        $totalPage = count($storeList);

        $offset = ($curPage - 1) * $pageSize;
        $storeList = array_slice($storeList, $offset, $pageSize);


        $data = [
            'list' => $storeList,
            'totalPage' => $totalPage,
        ];
        return $this->success($data);
    }

    /**
     * 获取筛选条件省、市、服务店铺列表
     */
    protected function _filterStoreList($city, $filter, $curPage, $pageSize)
    {

        $STORE_LIST = Redis::get('CSS_$STORE_LIST');
        $STORE_LIST = json_decode($STORE_LIST, true);
        $storeList = $STORE_LIST['storeList'];

        //单独城市筛选
        if ($city && count($filter) == 0) {
            $STORE_ID = Redis::get('CSS_$STORE_ID');
            $STORE_ID = json_decode($STORE_ID, true);
            $cityID = $STORE_ID['city'][$city];
            //筛选及数据分页处理
            $storeList = array_filter(array_values(array_intersect_key($storeList, $cityID)));
            $totalPage = count($storeList);
            $offset = ($curPage - 1) * $pageSize;
            $storeList = array_slice($storeList, $offset, $pageSize);
            $data = [
                'list' => $storeList,
                'totalPage' => $totalPage
            ];
            return $this->success($data);
        }

        //城市和服务筛选
        if ($city && count($filter) > 0) {
            $STORE_ID = Redis::get('CSS_$STORE_ID');
            $STORE_ID = json_decode($STORE_ID, true);
            $cityID = $STORE_ID['city'][$city];
            $serviceIDS = [];
            array_map(function ($i) use (&$serviceIDS, $STORE_ID) {
                // dump($STORE_ID['services'][$i]);
                $serviceIDS = array_merge($serviceIDS, $STORE_ID['services'][$i]);
            }, $filter);
            if (count($serviceIDS) > 0) {
                $serviceIDS = array_flip($serviceIDS);
            }


            $filterIds = array_filter(array_values(array_intersect_key($cityID, $serviceIDS)));
            if (count($filterIds) == 0) {
                return $this->success([]);
            } else {
                $filterIds = array_flip($filterIds);
                $storeList = array_filter(array_values(array_intersect_key($storeList, $filterIds)));
            }

            $totalPage = count($storeList);
            $offset = ($curPage - 1) * $pageSize;
            $storeList = array_slice($storeList, $offset, $pageSize);
            $data = [
                'list' => $storeList,
                'totalPage' => $totalPage
            ];
            return $this->success($data);

        }


    }


    function storeInitCheck()
    {

        $filter = request('filter', '[]');
        $filter = json_decode($filter, true);

        $serviceIDS = [];

        if (count($filter) > 0) {
            $STORE_ID = Redis::get('CSS_$STORE_ID');
            //店铺id映射
            $STORE_ID = json_decode($STORE_ID, true);
            array_map(function ($key) use (&$serviceIDS, $STORE_ID) {
                $serviceIDS = array_merge($serviceIDS, $STORE_ID['services'][$key]);
            }, $filter);
        }

        $STORE_LIST = Redis::get('CSS_$STORE_LIST');
        $STORE_LIST = json_decode($STORE_LIST, true);
        $allStoreList = $STORE_LIST['storeList'];

        if ($serviceIDS) {
            $storeList = array_filter(array_values(array_intersect_key($allStoreList, array_flip($serviceIDS))));
        } else {
            $storeList = [];
        }

        $city = [];
        foreach ($storeList as $store) {
            $city[$store['address']['state']][] = $store['address']['city'];
        }

        $city = array_map(function ($item) {
            return array_values(array_unique($item));
        }, $city);


        /*
        $data= Redis::get('CSS_$STORE_LIST');
        $data =json_decode($data,true);
        $servicesListOption=[];
        foreach($data['servicesList'] as $v){
            $servicesListOption[]=[
                'key'=>$v,
                'name'=>$v
            ];
        }
        */

        //unset($city['湖北']);
        $data = [
            'list' => $city,
            // 'fillter'=>$servicesListOption
        ];
        return $this->success($data);
    }


    function storeInit()
    {
//        $list = [
//            '上海'=>['上海市'],
//            '北京'=>['北京市'],
//            '新疆维吾尔自治区'=>[
//                "塔城市",
//                "乌苏市",
//                "额敏县",
//                "沙湾县",
//                "托里县",
//                "裕民县",
//                "和布克赛尔蒙古自治县"
//            ],
//            '江苏省'=>[
//            '苏州市',
//            '南京市'
//             ],
//            '广东省'=>[
//            '广州市',
//            '中山市'
//            ]
//        ];
//        $filter=[
//            ['key'=>'1','name'=>'分店代收eshop退货'],
//            ['key'=>'2','name'=>'分店收货'],
//            ['key'=>'现场刻字','name'=>'现场刻字'],
//            ['key'=>'钟表','name'=>'钟表'],
//        ];


        $data = Redis::get('CSS_$STORE_LIST');
        $data = json_decode($data, true);
        $servicesListOption = [];
        foreach ($data['servicesList'] as $v) {
            $servicesListOption[] = [
                'key' => $v,
                'name' => $v
            ];
        }

        $cityTree = array_map(function ($state) {
            return array_values(array_unique($state));
        }, $data['cityTree']);

        //unset($cityTree['湖北']);

        $data = [
            'list' => $cityTree,
            'fillter' => $servicesListOption
        ];
        return $this->success($data);


    }

    function _getCity($latitude, $longitude)
    {
        $location = trim($latitude) . ',' . trim($longitude);
        //$location = '39.984154,116.307490';
        $response = $this->_location($location);
        if ($response['status'] === 0) {
            $city = $response['result']['address_component']['city'];
            return $city;
        } else {
            return false;
        }
    }

    function getCity()
    {
        $data = request()->all();
        $validator = Validator::make($data, [
                'latitude' => 'required',
                'longitude' => 'required'
            ]
        );
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $city = $this->_getCity($data['latitude'], $data['longitude']);
        if ($city) {
            return $this->success(['city' => $city]);
        } else {
            return $this->error('获取城市错误');
        }
    }


    function redis()
    {

        $STORE_LIST = json_decode(Redis::get('CSS_$STORE_LIST'), true);
        $IDS = json_decode(Redis::get('CSS_$STORE_ID'), true);

        $data = ['STORE_LIST' => $STORE_LIST, 'IDS' => $IDS];
        return $this->success($data);
    }


    /**
     * 逆地址解析(坐标位置描述);
     * 通过经纬度获取位置信息
     * @param array $location
     * @return mixed
     * location=lat<纬度>,lng<经度>
     * 39.984154,116.307490
     */
    protected function _location($location)
    {
        $url = 'https://apis.map.qq.com/ws/geocoder/v1/?key=' . self::KEY . '&location=' . $location;
        $locationInfo = $this->curl($url);
        return $locationInfo;
    }


    /**
     * 距离计算（一对多）
     * @param array $from
     * @param array $to
     * @param $mode :
     * driving：驾车
     * walking：步行
     * bicycling：自行车
     */
    protected function _distance($from, Array $to, $mode = 'walking')
    {
        $toString = join(';', $to);
        $url = 'http://apis.map.qq.com/ws/distance/v1/?key=' . self::KEY . '&mode=' . $mode . "&from=" . $from . "&to=" . $toString;
        $response = $this->curl($url);
        return $response;
    }

    protected function curl($url, $data = [])
    {
        $data = json_encode($data);
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            $result = curl_exec($ch);
            $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $resultArray = \json_decode($result, true);
            $logger = new GoodsCurlLog();
            $logger->curlLog(json_decode($data, true), "GET", $url,
                "", $return_code, $result);
            return $resultArray;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

}