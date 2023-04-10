<?php
namespace App\Service\DashBoard;

use Illuminate\Support\Facades\DB;

//dashboard 小程序访问数据
class Miniapp
{
    public $wx_small_daily_summary_table = 'wx_small_daily_summary';
    public $wx_small_visit_page_table = 'wx_small_visit_page';

    // TODO,路径和页面的映射关系
    public $path_name_mapping = [

    ];

    public function getData($searchStartDate,$searchEndDate){
        $sql = "SELECT sum(visit_uv) as uv,sum(visit_pv) as pv, avg(stay_time_uv) as avg_stay_time  FROM ".$this->wx_small_daily_summary_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" ";
        $pv_uv = DB::select($sql);
        $pv_uv = json_decode(json_encode($pv_uv),true);
        $data['uv'] = (int) $pv_uv[0]['uv'];
        $data['pv'] = (int) $pv_uv[0]['pv'];
        $data['avg_stay_seconds'] = (int) $pv_uv[0]['avg_stay_time'];
        $sql = "SELECT sum(page_visit_pv) pv,page_path  FROM ".$this->wx_small_visit_page_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" group by page_path order by pv desc limit 10 ";
        $top_page_view = DB::select($sql);
        $top_page_view = $this->convertPathToName($top_page_view);
        $data['top_page_view'] = $top_page_view;
        $sql = "SELECT sum(page_visit_uv) as uv FROM ".$this->wx_small_visit_page_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" and page_path = 'pages/pdt-detail/pdt-detail'";
        $pdt_uv = DB::select($sql);
        $pdt_uv = json_decode(json_encode($pdt_uv),true);
        $data['pdt_uv'] = (int) $pdt_uv[0]['uv'];

        return $data;
    }

    public function convertPathToName($top_page_view){
        $mapping = $this->path_name_mapping;
        $data = [];
        foreach ($top_page_view as $item){
            $item = (array) $item;
            $name = $mapping[$item['page_path']]??($this->defaultPathName($item['page_path']));
            $item['name'] = $name;
            $data[] = $item;
        }
        return $data;
    }
    private function defaultPathName($path){
        $path_arr = explode('/',$path);
        if(!is_array($path_arr)){
            return $path;
        }
        return array_pop($path_arr);

    }

}