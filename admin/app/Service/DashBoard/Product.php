<?php
namespace App\Service\DashBoard;

use Illuminate\Support\Facades\DB;
use App\Lib\Http;

//dashboard 商品数据
class Product
{
    public $prod_view_table = 'prod_view_statistics';
    public $cat_view_table = 'prod_view_by_cat_statistics';
    public $share_table = 'prod_share_statistics';
    public $search_table = 'prod_keywords_statistics';
    public $add_cart_table = 'prod_add_cart_statistics';

    public function getData($searchStartDate,$searchEndDate){
        $sql = "SELECT pdtId,\"prodName\",sum(day_view_times)as scores FROM ".$this->prod_view_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" GROUP BY pdtId ORDER BY scores desc limit 3";
        $top_prod_view = DB::select($sql);
        $top_prod_view = json_decode(json_encode($top_prod_view),true);
        $data['top_prod_view'] = $top_prod_view;
        $sql = "SELECT pdtId,\"prodName\",sum(day_share_times)as scores FROM ".$this->share_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" GROUP BY pdtId ORDER BY scores desc limit 3";
        $top_share = DB::select($sql);
        $top_share = json_decode(json_encode($top_share),true);
        $data['top_share'] = $top_share;
        $sql = "SELECT prod_cat_id,\"prod_cat_name\",sum(view_times)as scores FROM ".$this->cat_view_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" GROUP BY prod_cat_id ORDER BY scores desc limit 3";
        $top_cat_view = DB::select($sql);
        $top_cat_view = json_decode(json_encode($top_cat_view),true);
        $data['top_cat_view'] = $top_cat_view;
        $sql = "SELECT keywords as keyword ,sum(search_time)as scores FROM ".$this->search_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" GROUP BY keywords ORDER BY scores desc limit 10";
        $top_search = DB::select($sql);
        $top_search = json_decode(json_encode($top_search),true);
        $data['top_search'] = $top_search;
        $sql = "SELECT sum(day_add_times)as scores FROM ".$this->add_cart_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" ";
        $add_cart_times = DB::select($sql);
        $add_cart_times = json_decode(json_encode($add_cart_times),true);
        $data['add_cart_times'] = (int) $add_cart_times[0]['scores'];
        $data = $this->getProductInfo($data);
        $data = $this->getCatInfo($data);
        return $data;
    }

    /*
     * 获取商品访问次数每天top10
     */
    public  function getProdView($searchStartDate,$searchEndDate,$offset,$limit){
        $countSql = "SELECT count(*) as c FROM ".$this->prod_view_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\"" ;
        $top_prod_view_count = DB::select($countSql);
        $sql = "SELECT pdtId,\"prodName\",sum(day_view_times)as scores,ref_date FROM ".$this->prod_view_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" GROUP BY pdtId ,ref_date ORDER BY ref_date desc,scores desc  limit $offset ,". $limit;
        $top_prod_view = DB::select($sql);
        $top_prod_view = json_decode(json_encode($top_prod_view),true);
        $data['top_prod_view'] = $top_prod_view;
        $data['top_share'] = [];
        $data = $this->getProductInfo($data);
        $data['count'] = $top_prod_view_count[0]->c ?? 0;
        return $data;
    }

    /*
    * 获取商品分享每天top10
    */
    public  function getProdShareView($searchStartDate,$searchEndDate,$offset,$limit){
        $countSql = "SELECT count(*) as c FROM ".$this->share_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\"" ;
        $top_share_count = DB::select($countSql);
        $sql = "SELECT pdtId,\"prodName\",sum(day_share_times)as scores,ref_date FROM ".$this->share_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" GROUP BY pdtId ,ref_date ORDER BY ref_date desc,scores desc  limit $offset ,". $limit;
        $top_share_view = DB::select($sql);
        $top_share_view = json_decode(json_encode($top_share_view),true);
        $data['top_share'] = $top_share_view;
        $data['top_prod_view'] = [];
        $data = $this->getProductInfo($data);
        $data['count'] = $top_share_view[0]->c ?? 0;
        return $data;
    }

    /*
    * 获取商品分类每天top10
    */
    public  function getProdCatView($searchStartDate,$searchEndDate,$offset,$limit){
        $countSql = "SELECT count(*) as c FROM ".$this->cat_view_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\"" ;
        $top_cart_view_count = DB::select($countSql);
        $sql = "SELECT prod_cat_id,\"prod_cat_name\",sum(view_times)as scores,ref_date FROM ".$this->cat_view_table." WHERE ref_date BETWEEN \"$searchStartDate\" AND \"$searchEndDate\" GROUP BY prod_cat_id ,ref_date ORDER BY ref_date desc,scores desc  limit $offset ,". $limit;
        $top_cart_view = DB::select($sql);
        $top_cart_view = json_decode(json_encode($top_cart_view),true);
        $data['top_cat_view'] = $top_cart_view;
        $data = $this->getCatInfo($data);
        $data['count'] = $top_cart_view_count[0]->c ?? 0;
        return $data;
    }

    public function getCatInfo($data){
        $tree = (new Http)->curl('goods/product/getCategoryTree');
        $cat_arr = $this->convertTreeToFlatArray($tree['data']);
        $top_cat_view = $data['top_cat_view'];
        $new_top_cat_view = [];
        foreach ($top_cat_view as $item){
            $cid = $item['prod_cat_id'];
            $cname = $cat_arr[$cid]??'';
            $item['prod_cat_name'] = $cname;
            $new_top_cat_view[] = $item;
        }
        $data['top_cat_view'] = $new_top_cat_view;
        return $data;
    }

    private function convertTreeToFlatArray($tree,$arr=[]){
        foreach($tree as $item){
            $id = $item['id'];
            $name = $item['label'];
            $arr[$id] = $name;
            if(isset($item['children']) and is_array($item['children'])){
                $arr = $this->convertTreeToFlatArray($item['children'],$arr);
            }
        }
        return $arr;
    }

    public function getProductInfo($data){
        $top_prod_view = $data['top_prod_view'];
        $top_share = $data['top_share'];
        $pids = [];
        foreach ($top_prod_view as $item){
            $pids[] = $item['pdtId'];
        }

        foreach($top_share as $item){
            $pids[] = $item['pdtId'];
        }
        $products = (new Http)->curl('outward/product/getProduct',['ids'=>implode(',',$pids)]);
        $products = $products['data'];
        $product_id_name = [];
        foreach($products as $item){
            $pid = $item['unique_id'];
            $pname = $item['product_name'];
            $product_id_name[$pid] = $pname;
        }
        $new_top_prod_view = [];
        foreach ($top_prod_view as $item){
            $pid = $item['pdtId'];
            $item['prodName'] = $product_id_name[$pid]??'';
            $new_top_prod_view[] = $item;
        }
        $new_top_share = [];
        foreach($top_share as $item){
            $pid = $item['pdtId'];
            $item['prodName'] = $product_id_name[$pid]??'';
            $new_top_share[] = $item;
        }
        $data['top_prod_view'] = $new_top_prod_view;
        $data['top_share'] = $new_top_share;
        return $data;
    }

}