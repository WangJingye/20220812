<?php namespace App\Services\Top\Method;

use App\Services\Top\TopAbstract;
use App\Repositories\Product\ProductRepository;

class TaobaoItemsOnsaleGet extends TopAbstract
{
    /**
     * 获取当前会话用户出售中的商品列表
     * 接口文档：https://open.taobao.com/api.htm?spm=a219a.7386797.0.0.1a79669aqUtUe1&source=search&docId=18&docType=2
     * @return array
     */
    public function execute():array
    {
        $params = $this->request->all();
        $fields = $params['fields']??'';
        $page_no = (int) ($params['page_no']??1);
        $page_size = (int) ($params['page_size']??40);
        $fileds_arr = explode(',',$fields);
        
        $product_info = (new ProductRepository())->getAllProducts($page_no,$page_size);
        $total_count = $product_info['total'];
        $product_data = $product_info['data'];
        
        $items = [];
        foreach($product_data as $item){
            $product_id = $item->entity_id;
            $data = [
                'iid'=>$product_id,
                'num_iid'=>$product_id,
                'title'=>'',
                'nick'=>'',
                'type'=>'fixed',
                'cid'=>0,
                'seller_cids'=>'',
                'props'=>'',
                'pic_url'=>'',
                'num'=>0,
                'valid_thru'=>0,
                'list_time'=>'2009-10-22 14:22:06',
                'delist_time'=>'2000-01-01 00:00:00',
                'price'=>0,
                'has_discount'=>true,
                'has_invoice'=>true,
                'has_warranty'=>true,
                'has_showcase'=>true,
                'modified'=>'2000-01-01 00:00:00',
                'approve_status'=>'onsale',
                'postage_id'=>0,
                'outer_id'=>'',
                'is_virtual'=>true,
                'is_taobao'=>true,
                'is_ex'=>true,
            ];
            $return = [];
            foreach($fileds_arr as $field){
                $return[$field] = $data[$field];
            }
            $items[] = $return;
        }
        return [
            'items_inventory_get_response'=>[
                'items'=>[
                    'item'=>$items
                ],
                'total_results'=>$total_count,
            ]
        ];
    }
}
