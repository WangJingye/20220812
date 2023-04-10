<?php
namespace App\Service\Store;
use App\Model\Store;

class StoreService
{

    //更新store 的坐标
    public static function updateStoreMap()
    {
        $list = \DB::table('tb_store')->where('status',0)->limit(20)->get()->toArray(); 
        if(empty($list)) return true;
        foreach ($list as $key => $value) {
            //请求百度地图接口  获取坐标信息
            $search = array(" ","　","\n","\r","\t");
            $replace = array("","","","","");
            $str =  str_replace($search, $replace, $value->address);
            //$str = str_replace(chr(194) . chr(160), "a", $value->address);  // 解决方法1
            //$str = preg_replace('/\xC2\xA0/is', "a", $str); 
            \Log::info($str.'<br>');
            $info = file_get_contents('http://api.map.baidu.com/geocoder?address='.$str.'&output=json&src=webapp.baidu.openAPIdemo');
            \Log::info('地址=' . $info);
            $result = json_decode($info,true);
            //dd($result);
            if($result['status'] == 'OK')
            {
                $map[] = [
                    'id' => $value->id,
                    'lat' => $result['result']['location']['lat'],
                    'lon' => $result['result']['location']['lng'],
                    'status' =>1
                ];
            }
        }
        \log::info('获取坐标信息=', [$map]);
        return self::updateBatch($map);
    }


    //批量更新
    public static function updateBatch($multipleData = [])
    {
        if (empty($multipleData)) {
                \Log::info("批量更新数据为空");
                return false;
            }
            $tableName = 'tb_store'; // 表名
        if( $tableName && !empty($multipleData) ) {
            // column or fields to update
            $updateColumn = array_keys($multipleData[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";
            $q = "UPDATE ".$tableName." SET ";
            foreach ( $updateColumn as $uColumn ) {
                $q .=  $uColumn." = CASE ";

                foreach( $multipleData as $data ) {
                    $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
                }
                $q .= "ELSE ".$uColumn." END, ";
            }
            foreach( $multipleData as $data ) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";
            //$this->info($q);
            return \DB::update(\DB::raw($q));

        } else {
            return false;
        }
    }

    /**
     * 根据门店id获得城市信息
     * @param array $data
     */
    public static function getCityFromStoreId($storeIds = [])
    {
        if (empty($storeIds) || !is_array($storeIds)) {
            return;
        }

        $storeMode = new Store();

        $citys = $storeMode->whereIn('id', $storeIds)->get(['city', 'province', 'id', 'store_code'])->toArray();
        return array_column($citys, null, 'id');
    }


    /**
     * 获得全部城市
     */
    public static function getCity($where = [])
    {
        $storeMode = new Store();
        $citys = empty($where) ? $storeMode : $storeMode->where($where);
        $citys = $citys->get(['city'])->toArray();
        return array_values(array_unique(array_column($citys, 'city')));

    }


    /**
     * 获取全部门店
     */
    public static function getStore($where = [])
    {
        $storeMode = new Store();
        $stores = empty($where) ? $storeMode : $storeMode->where($where);
        $stores = $stores->get(['id', 'store_name'])->toArray();
        return $stores;
    }

    /**
     * 获取全部省
     */

    public static function getProvince()
    {
        $storeMode = new Store();
        $provinces = $storeMode->get(['province'])->toArray();
        return array_values(array_unique(array_column($provinces, 'province')));
    }


    public static function getCodeFromId($store_id = 0)
    {
        if ($store_id == 0) {
            return false;
        }
        $storeMode = new Store();
        $code = $storeMode->where([['id', '=', $store_id]])->get(['store_code'])->toArray();
        return $code;
    }

}

