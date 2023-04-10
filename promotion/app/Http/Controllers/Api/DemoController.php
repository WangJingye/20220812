<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\Activity;
use App\Model\Product;
use App\Http\Controllers\Api\MapController;
use Validator;
use App\Model\Promotion\Category;
use App\Model\Promotion\Cart;
use App\Model\Promotion\Coupon;

class DemoController extends Controller
{
	
    function category(){
        
        $data = [
            ['id'=>'1','name'=>'category 1','parentId'=>0,"checkArr"=>['checked'=>0]],
            ['id'=>'2','name'=>'category 2','parentId'=>0,"checkArr"=>['checked'=>0]],
            ['id'=>'3','name'=>'category 3','parentId'=>1,"checkArr"=>['checked'=>0]],
            ['id'=>'4','name'=>'category 4','parentId'=>3,"checkArr"=>['checked'=>0]],
            ['id'=>'5','name'=>'category 5','parentId'=>4,"checkArr"=>['checked'=>0]],
            ['id'=>'6','name'=>'category 6','parentId'=>5,"checkArr"=>['checked'=>0]],
//             ['id'=>'7','name'=>'category 7','parentId'=>6,"checkArr"=>['checked'=>0]],
//             ['id'=>'8','name'=>'category 8','parentId'=>2,"checkArr"=>['checked'=>0]],
//             ['id'=>'9','name'=>'category 9','parentId'=>8,"checkArr"=>['checked'=>0]],
//             ['id'=>'10','name'=>'category 10','parentId'=>9,"checkArr"=>['checked'=>0]],
//             ['id'=>'11','name'=>'category 11','parentId'=>10,"checkArr"=>['checked'=>0]],
//             ['id'=>'12','name'=>'category 12','parentId'=>11,"checkArr"=>['checked'=>0]],
        ];
        //$data= $this->toTree($data);
        
        return [
            'status'=>['code'=>0,'message'=>'操作成功'],
            'data'=>$data
        ];
    }
    
    function product(){
        $productArray=[
            ['sku'=>'sku_1','name'=>'商品1'],
            ['sku'=>'sku_2','name'=>'商品2'],
            ['sku'=>'sku_3','name'=>'商品3'],
            ['sku'=>'sku_4','name'=>'商品4'],
            ['sku'=>'sku_5','name'=>'商品5'],
            ['sku'=>'sku_6','name'=>'商品6'],
            ['sku'=>'sku_7','name'=>'商品7'],
            ['sku'=>'sku_8','name'=>'商品8'],
            ['sku'=>'sku_9','name'=>'商品9'],
            ['sku'=>'sku_10','name'=>'商品10'],
            ['sku'=>'sku_11','name'=>'商品11'],
            ['sku'=>'sku_12','name'=>'商品12'],
            ['sku'=>'sku_13','name'=>'商品13'],
            ['sku'=>'sku_14','name'=>'商品14'],
            ['sku'=>'sku_15','name'=>'商品15'],
            ['sku'=>'sku_16','name'=>'商品16'],
            ['sku'=>'sku_17','name'=>'商品17'],
            ['sku'=>'sku_18','name'=>'商品18'],
            ['sku'=>'sku_19','name'=>'商品19'],
            ['sku'=>'sku_20','name'=>'商品20'],
            ['sku'=>'sku_21','name'=>'商品21'],
            ['sku'=>'sku_22','name'=>'商品22'],
            ['sku'=>'sku_23','name'=>'商品23'],
            ['sku'=>'sku_24','name'=>'商品24'],
        ];
        
        $data =[
            'code'=>0,
            'msg'=>'产品数据',
            'data'=>$productArray
        ];
        return $data;
    }
    
    function getCategoryBySku(){
        $data=[
            'sku_1'=>[1,2],
            'sku_2'=>[3,4],
            'sku_3'=>[]
        ];
        return $this->success($data, 'ok');
    }
    
    function getPriceBySku(){
        $productArray=[
            ['sku'=>'sku_1','name'=>'商品1','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_2','name'=>'商品2','price'=>'299.99','spricePrice'=>'199.99','memberPrice'=>'198.98'],
            ['sku'=>'sku_3','name'=>'商品3','price'=>'399.99','spricePrice'=>'299.99','memberPrice'=>'298.98'],
            ['sku'=>'sku_4','name'=>'商品4','price'=>'499.99','spricePrice'=>'399.99','memberPrice'=>'398.98'],
            ['sku'=>'sku_5','name'=>'商品5','price'=>'599.99','spricePrice'=>'499.99','memberPrice'=>'498.98'],
            ['sku'=>'sku_6','name'=>'商品6','price'=>'699.99','spricePrice'=>'599.99','memberPrice'=>'598.98'],
            ['sku'=>'sku_7','name'=>'商品7','price'=>'799.99','spricePrice'=>'699.99','memberPrice'=>'698.98'],
            ['sku'=>'sku_8','name'=>'商品8','price'=>'899.99','spricePrice'=>'799.99','memberPrice'=>'798.98'],
            ['sku'=>'sku_9','name'=>'商品9','price'=>'999.99','spricePrice'=>'899.99','memberPrice'=>'898.98'],
            ['sku'=>'sku_10','name'=>'商品10','price'=>'199.99','spricePrice'=>'999.99','memberPrice'=>'998.98'],
            ['sku'=>'sku_11','name'=>'商品11','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_12','name'=>'商品12','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_13','name'=>'商品13','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_14','name'=>'商品14','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_15','name'=>'商品15','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_16','name'=>'商品16','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_17','name'=>'商品17','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_18','name'=>'商品18','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_19','name'=>'商品19','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_20','name'=>'商品20','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_21','name'=>'商品21','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_22','name'=>'商品22','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_23','name'=>'商品23','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
            ['sku'=>'sku_24','name'=>'商品24','price'=>'199.99','spricePrice'=>'99.99','memberPrice'=>'98.98'],
        ];
        return $this->success($productArray, 'ok');
    }
    
    function demo(){
            $coupon=new Coupon();
            $result=$coupon->generatePool(2,30);
            foreach($result as $code){
                $coupon::create([
                    'name'=>'购物卷',
                    'code'=>$code
                ]);
            }
    }
    
    
    function toTree($list=null, $pk='id',$pid = 'parentId',$child = 'children',$root=0)
    {
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
    

    


}
