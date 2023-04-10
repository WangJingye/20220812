<?php namespace App\Service\Goods;

use App\Model\Goods\{Category,ProductCat,Spu,Sku};

class DataService
{
    //分类图片存放的目录
    public static $cat_dir = 'tp_upload/goods/CAT_PICS';
    //商品列表图片存放的目录
    public static $spu_list_dir = 'tp_upload/goods/PLP_SPU_PICS';
    //商品详情图片存放的目录
    public static $spu_detail_dir = 'tp_upload/goods/PDP_SPU_PICS';
    /**
     * @param $data
     * @return bool|string
     */
    public static function cat_import($data){
        try {
            $img_src = env('OSS_DOMAIN').'/'.self::$cat_dir.'/';
            $p_cats = [];
            foreach($data as $item){
                $p_cat_code = $item[0];
                $p_cat_name = $item[1];
                $p_cat_pic = $item[2];
                if(!array_key_exists($p_cat_code,$p_cats)){
                    $p_cat_data = [
                        'cat_name'=>$p_cat_name,
                        'parent_cat_id'=>0,
                        'status'=>1,
                        'cat_kv_image'=>$img_src.$p_cat_pic,
                        'share_content'=>$p_cat_name,
                        'cat_desc'=>$p_cat_name,
                        'cat_type'=>1,
                    ];
                    //无则新增有则更新
                    $pid = Category::query()->where('cat_code',$p_cat_code)->value('id');
                    if($pid){
                        Category::query()->find($pid)->update($p_cat_data);
                    }else{
                        $p_cat_data['cat_code'] = $p_cat_code;
                        $pid = Category::query()->insertGetId($p_cat_data);
                    }
                    $p_cats[$p_cat_code] = $pid;
                }else{
                    $pid = $p_cats[$p_cat_code];
                }
                //更新二级目录
                $cat_code = $item[3];
                $cat_name = $item[4];
                $cat_pic = $item[5];
                $cat_data = [
                    'cat_name'=>$cat_name,
                    'parent_cat_id'=>$pid,
                    'status'=>1,
                    'cat_kv_image'=>$img_src.$cat_pic,
                    'share_content'=>$cat_name,
                    'cat_desc'=>$cat_name,
                    'cat_type'=>1,
                ];
                $id = Category::query()->where('cat_code',$cat_code)->value('id');
                if($id){
                    Category::query()->find($id)->update($cat_data);
                }else{
                    $cat_data['cat_code'] = $cat_code;
                    Category::query()->insert($cat_data);
                }
            }return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public static function spu_import($data){
        try {
            $spus = [];
            foreach($data as $item){
                $sku_id = "{$item[0]}";
                $spec_code = $item[1];
                $spec_desc = $item[2];
                $spu_code = $item[3];
                $ori_price = "$item[4]";
                $spu_name = $item[5];
                $spec = $item[6];
                $spu_sub_name = $item[7]??'';
                $spu_desc = $item[8]??'';
                $is_gift = array_get($item,'9')=='Y'?1:0;
                $spec2 = $item[10];
                $spec_code2 = $item[11];
                $spec_desc2 = $item[12];

                $product_id = $spu_code;
                if(!array_key_exists($product_id,$spus)){
                    //保存或更新spu
                    $spu_data = [
                        'product_name'=>$spu_name,
                        'product_desc'=>$spu_desc,
                        'short_product_desc'=>$spu_sub_name,
                        'is_gift_box'=>$is_gift,
                        'can_search'=>$ori_price?1:0,
                        'status'=>1
                    ];

                    $spec_type = [];
                    //支持同时可存在两个规格
                    if($spec=='category_ml'){
                        $spec_type[] = 'capacity_ml';
                    }elseif($spec=='category_scent'){
                        $spec_type[] = 'capacity_g';
                    }
                    if($spec2=='category_ml'){
                        $spec_type[] = 'capacity_ml';
                    }elseif($spec2=='category_scent'){
                        $spec_type[] = 'capacity_g';
                    }
                    if(count($spec_type)){
                        $spec_type = array_unique($spec_type);
                        $spu_data['spec_type'] = implode(',',$spec_type);
                    }

                    $spu_id = Spu::query()->where('product_id',$product_id)->value('id');
                    if($spu_id){
                        Spu::query()->find($spu_id)->update($spu_data);
                    }else{
                        $spu_data['product_id'] = $product_id;
                        $spu_id = Spu::query()->insertGetId($spu_data);
                    }
                    $spus[$product_id] = $spu_id;
                }else{
                    $spu_id = $spus[$product_id];
                }
                //保存或更新sku
                $sku_data = [
                    'ori_price'=>$ori_price,
                    'product_idx'=>$spu_id
                ];
                if($spec=='category_ml'){
                    $sku_data['spec_capacity_ml_code'] = $spec_code;
                    $sku_data['spec_capacity_ml_code_desc'] = $spec_desc;
                }elseif($spec=='category_scent'){
                    $sku_data['spec_capacity_g_code'] = $spec_code;
                    $sku_data['spec_capacity_g_code_desc'] = $spec_desc;
                }
                if($spec2=='category_ml'){
                    $sku_data['spec_capacity_ml_code'] = $spec_code2;
                    $sku_data['spec_capacity_ml_code_desc'] = $spec_desc2;
                }elseif($spec2=='category_scent'){
                    $sku_data['spec_capacity_g_code'] = $spec_code2;
                    $sku_data['spec_capacity_g_code_desc'] = $spec_desc2;
                }
                $sku_iid = Sku::query()->where('sku_id',$sku_id)->value('id');
                if($sku_iid){
                    Sku::query()->find($sku_iid)->update($sku_data);
                }else{
                    $sku_data['sku_id'] = $sku_id;
                    Sku::query()->insert($sku_data);
                }
            }return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @param $data
     * @return bool|string
     */
    public static function img_import($data){
        try {
            $spu_list_src = env('OSS_DOMAIN').'/'.self::$spu_list_dir.'/';
            $spu_detail_src = env('OSS_DOMAIN').'/'.self::$spu_detail_dir.'/';
            foreach($data as $item){
                $product_id = $item[0];

                $list_img = $item[1];
//                $info = pathinfo($list_img);
//                $ext = strtolower($info['extension']);
                $list_img = $spu_list_src.$list_img;

                $kv_images = [];
                for($i=2;$i<=7;$i++){
                    if(!empty($item[$i])){
                        $img = $item[$i];
                        $info = pathinfo($img);
                        $ext = strtolower($info['extension']);
                        if(in_array($ext,['jpg','jpeg','png'])){
                            $kv_images[] = [
                                'tag'=>'image',
                                'data'=>['src'=>$spu_detail_src.$img],
                            ];
                        }
                    }
                }
                $kv_images = json_encode($kv_images);
                Spu::query()->where('product_id',$product_id)->update(compact('list_img','kv_images'));
            }return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public static function rel_import($data){
        try {
            ProductCat::query()->truncate();
            $rel = $spus = $cats = [];
            foreach($data as $item){
                $spu_code = $item[0];
                $cat_code = $item[1];

                $spus[] = $spu_code;
                $cats[] = $cat_code;
                //关系唯一
                $key = "{$spu_code}_{$cat_code}";
                $rel[$key] = [
                    'spu_code'=>$spu_code,
                    'cat_code'=>$cat_code,
                ];
            }
            $spus_arr = Spu::query()->whereIn('product_id',$spus)->pluck('id','product_id')->toArray();
            $cats_arr = Category::query()->whereIn('cat_code',$cats)->pluck('id','cat_code')->toArray();
            $rel_data = [];
            foreach($rel as $item){
                $spu_id = array_get($spus_arr,$item['spu_code']);
                $cat_id = array_get($cats_arr,$item['cat_code']);
                if($spu_id && $cat_id){
                    $rel_data[] = [
                        'product_idx'=>$spu_id,
                        'cat_id'=>$cat_id,
                        'type'=>1,
                    ];
                }
            }
            if(count($rel_data)){
                ProductCat::query()->insert($rel_data);
            }
            return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public static function key_import($data){
        try {
            foreach($data as $item){
                Spu::query()->where('product_id',$item[0])->update(['custom_keyword'=>$item[1]]);
            }
            return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public static function sort_import($data){
        try {
            foreach($data as $item){
                Spu::query()->where('product_id',$item[0])->update(['sort'=>$item[1]]);
            }
            return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public static function price_import($data){
        try {
            foreach($data as $item){
                $sku_id = "{$item[0]}";
                $ori_price = "$item[1]";

                //保存或更新sku
                $sku_data = [
                    'ori_price'=>$ori_price,
                ];
                $sku_iid = Sku::query()->where('sku_id',$sku_id)->value('id');
                if($sku_iid){
                    Sku::query()->find($sku_iid)->update($sku_data);
                }
            }return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

}
