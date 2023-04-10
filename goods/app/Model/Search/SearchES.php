<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/11/6
 * Time: 16:17
 */

namespace App\Model\Search;
use App\Model\Help;
use App\Model\Search\EsResult;

class SearchES extends Model
{

    const CAN_SORT_FIELD = ['lowest_price'];

    //聚簇商品属性，区分商品有哪些可以删选的项
    public static $filter = [
        'aggs' => [
            'golden' => [
                'filter'=> [
                    'bool' => [
                        'must' => [
                            'terms' => [
                                'gold_type_code' => [ '24KG', '24GG', '23KG']
                            ]
                        ]
                    ]
                ]
            ],
            '18k'=> [
                'filter'=> [
                    'bool' => [
                        'must' => [
                            'terms' => [
                                'gold_type_code' => [
                                    '18YW', '18YR', '18YO', '18YB', '18WR', '18WO', '18WB',
                                    '18TT', '18RB', '18OR', '18KY', '18KW', '18KS', '18KR',
                                    '18KO', '18KG', '18KB', '14YW', '14WR', 'M950', 'M900', 'M850',
                                ],
                            ]
                        ]
                    ]
                ]
            ],
            'pt' => [
                'filter'=> [
                    'bool' => [
                        'must' => [
                            'terms' => [
                                'gold_type_code' => [
                                    'P999', 'P950', 'P900', 'P850', 'P585',
                                    'P500', 'M950', 'M900', 'M850',
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'silver' => [
                'filter'=> [
                    'bool' => [
                        'must' => [
                            'terms' => [
                                'gold_type_code' => ['SILV', 'S999']
                            ]
                        ]
                    ]
                ]
            ],
            'diamond' => [
                'filter'=> [
                    'bool' => [
                        'must' => [
                            'terms' => [
                                'product_type' => ['DF', 'DI']
                            ]
                        ]
                    ]
                ]
            ],
            'pearl' => [
                'filter'=> [
                    'bool' => [
                        'must' => [
                            'terms' => [
                                'product_type' => ['TF', 'PL']
                            ]
                        ]
                    ]
                ]
            ],
            'gem' => [
                'filter'=> [
                    'bool' => [
                        'must' => [
                            'terms' => [
                                'product_type' => ['XF', 'GS', 'QF', 'SS']
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    public static $material = [
        "黄金" => [ "GA", "GB", "GI", "GF"],
        "铂金" => ["PA" ,"PF", "MP"],
        "K金"  => ["FJ", "MP"],
        "银饰" => ["SF"],
        "宝石" => ["XF", "GS", "QF", "SS"],
        "珍珠" => ["TF", "PL"],
        "钻石" => ["DF", "DI"]
    ];

    public static function SearchFromESByCatalog($catalog)
    {
        $params = [
            'index' => config("database.connections.elasticsearch.index"),
            'type'  => '_doc',
            'body'  => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' =>
                                ['usage' => $catalog]],
                        ],
                    ],
                ],
            ],
        ];

        $result = app('es')->search($params);
        $verificationResult = EsResult::verification($result);
        return $verificationResult;
    }



    /**
     * 通过多种组合匹配搜索，操作符为And保证结构精确
     * @param $request_info
     * @return array|bool
     */
    public static function SearchProductFromESByFilter($request_info)
    {
        $must[] = ['term' => ['status' => 1]];
        //大分类
        $cat_id = array_get($request_info,'cat_id')?:0;
        //如果筛选中已有分类则大分类不做筛选
        $sub_cat_id = array_get($request_info,'filter.cat');
        if(empty($sub_cat_id) && $cat_id){
            $must[]['nested'] = [
                'path'=>'cats_all',
                'query'=>['match'=>['cats_all.cat_id'=>$cat_id]]
            ];
        }
        //构建filter
        self::makeupFilter($request_info,$must,$sort);
        $keyword = self::makeUpKeyWord(array_get($request_info,'keyword'));

        $curPage = isset($request_info['page']) ? $request_info['page'] : 1;
        $size = isset($request_info['size']) ? $request_info['size'] : 10;
        $index = self::getIndexFromBrandCode();
        $params_multi_match = [
            'index' => $index,
            'type'  => '_doc',
            "size" => $size,
            'from' => ($curPage-1) * $size,
            //ES的搜索逻辑是从第0个商品开始搜索的
            'body'  => [
                'query' => [
                    'bool' => [
                        'filter'=>$keyword,
                        'must'=>$must
                    ]
                ],
                'track_scores' => true,
                '_source' => [
                   'unique_id', 'product_id' , 'status' , 'product_detail_wechat','product_detail_pc' , 'product_name'
                ],
                'sort' => $sort,
            ],
        ];
        //如果前端传了排序相关的2个字段，添加排序算法到ES请求中
        if (isset($request_info['sortKey']) && in_array($request_info['sortKey'],self::CAN_SORT_FIELD)  && isset($request_info['sort']) && in_array($request_info['sort'],['asc','desc']) ){
            unset($params_multi_match['body']['sort']);
            if($request_info['sortKey'] == 'lowest_price') $request_info['sortKey'] = 'lowest_ori_price';
            $params_multi_match['body']['sort'][] = [$request_info['sortKey'] => $request_info['sort']];
        }
        //聚合查询
        $aggs = [
            'specs_capacity_g_name'=>[
                'nested'=>['path'=>'specs_capacity_g'],
                'aggs'=>[
                    'spec_name'=>[
                        'terms'=>[
                            'field'=>'specs_capacity_g.value',
                            'size'=>10,
                            'order'=>['capacity_key'=>'asc']
                        ],
                        'aggs'=>['capacity_key'=>['avg'=>['field'=>'specs_capacity_g.key']]]
                    ]
                ]
            ],
            'specs_capacity_ml_name'=>[
                'nested'=>['path'=>'specs_capacity_ml'],
                'aggs'=>[
                    'spec_name'=>[
                        'terms'=>[
                            'field'=>'specs_capacity_ml.value',
                            'size'=>10,
                            'order'=>['capacity_key'=>'asc']
                        ],
                        'aggs'=>['capacity_key'=>['avg'=>['field'=>'specs_capacity_ml.key']]]
                    ]
                ]
            ]
        ];
        $params_multi_match['body']['aggs'] = $aggs;
        return app('es')->search($params_multi_match);
    }



    /**
     * 通过多种组合匹配搜索，操作符为And保证结构精确,通过分类搜索
     * @param $request_info
     * @return array|bool
     */
    public static function SearchCatalogFromESByFilter($request_info)
    {

        $categoryId = $request_info["categoryId"];
        $curPage = isset($request_info['curPage']) ? $request_info['curPage'] : 1;
        $fillterMaxPrice = isset($request_info['fillterMaxPrice']) ? $request_info['fillterMaxPrice'] : 1000000;
        $fillterMinPrice = isset($request_info['fillterMinPrice']) ? $request_info['fillterMinPrice'] : 0;
        $request_info['fillterMetal'] = isset($request_info['fillterMetal']) ? $request_info['fillterMetal'] : "";
        $request_info['fillterJewel'] = isset($request_info['fillterJewel']) ? $request_info['fillterJewel'] : "";
        $fillterMetalArray = (array) json_decode($request_info['fillterMetal'] ,true);
        $fillterJewelArray = (array) json_decode($request_info['fillterJewel'] ,true);
        $display_status = [  'term' => [   'display_status' => 1   ]  ];
        $size = 10; //每次显示10个商品
        $params_multi_match = [
            'index' => config("database.connections.elasticsearch.index"),
            'type'  => '_doc',
            "size" => $size,
            'from' => ($curPage-1) * $size,
            //ES的搜索逻辑是从第0个商品开始搜索的
            'body'  => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'multi_match' => [
                                'query' => $categoryId, //将关键词中包含的英文字母变为全部大写再去ES中检索
                                'type' => 'most_fields',
                                'fields' =>  [
                                    'categoryId' //分类ID
                                ],
                                'operator' => 'and',
                            ]
                        ],
                        'must' => [
                            'term' => [
                                'display_status' => 1
                                //必须可展示状态才能被搜索
                            ]
                        ],
                    ]
                ],
                'track_scores' => true,
                '_source' => [
                   'product_id' , 'display_status' , 'product_detail' , 'product_name'
                ],
                'sort' => [
                    'product_id' => "asc"
                ],
                'post_filter' => [
                    'bool' => [
                        'must' => [
                            '0' => [
                                'range' => [
                                    'price' => [
                                        'gte'=> $fillterMinPrice ,
                                        'lte'=> $fillterMaxPrice
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ],
        ];

        //如果前端传了排序相关的2个字段，添加排序算法到ES请求中
        if (isset($request_info['sortKey']) && isset($request_info['sort'])){
            unset($params_multi_match['body']['sort']);

            $sort_filter = [ 1 => "asc", 2 => "desc"];
            $params_multi_match['body']['sort'][] = [$request_info['sortKey'] => $sort_filter[$request_info['sort']]];
            $params_multi_match['body']['sort'][] = ['product_id' => $sort_filter[$request_info['sort']]];
        }


        //如果传递了fillterCategory，代表用途类型，需要先聚簇后过滤
        if(isset($request_info['fillterCategory']) && $request_info['fillterCategory'] !== "all"){
            //特殊处理下手链脚链这个选项，同时传了B/M两个用途的值过来
            if($request_info['fillterCategory'] == "B/M"){
                $fillterCategory = [
                    'terms' => [
                        'usage_code' => ["B","M"]
                    ]
                ];
            }else{
                $fillterCategory = [
                    'term' => [
                        'usage_code' => $request_info['fillterCategory']
                    ]
                ];
            }

            $params_multi_match['body']['post_filter']['bool']['must'][] = $fillterCategory;
        }

        if ( sizeof($fillterMetalArray) >0){
            $fillterMetal = [
                'terms' =>
                    self::searchByFilterMetal($fillterMetalArray)
            ];
            $params_multi_match['body']['post_filter']['bool']['must'][] = $fillterMetal;

        }

        if ( sizeof($fillterJewelArray) >0){
            $fillterJewel = [
                'terms' =>
                    self::searchByFilterJewel($fillterJewelArray)

            ];
            $params_multi_match['body']['post_filter']['bool']['must'][] = $fillterJewel;

        }


        $params_multi_match['body']['aggs'] =  self::$filter['aggs'];
        $result = app('es')->search($params_multi_match);
        //这里渠道的结果不仅有匹配商品的Product Id，也有聚簇后的gold_type及product_type
        return $result;
    }


    /**
     * 通过多种组合匹配搜索，操作符为And保证结构精确
     * @param $request_info
     * @return array|bool
     */
    public static function SearchProductByMaterial($request_info)
    {
        $keyword = $request_info["keyword"];
        $curPage = isset($request_info['curPage']) ? $request_info['curPage'] : 1;
        $fillterMaxPrice = isset($request_info['fillterMaxPrice']) ? $request_info['fillterMaxPrice'] : 1000000;
        $fillterMinPrice = isset($request_info['fillterMinPrice']) ? $request_info['fillterMinPrice'] : 0;
        $request_info['fillterMetal'] = isset($request_info['fillterMetal']) ? $request_info['fillterMetal'] : "";
        $request_info['fillterJewel'] = isset($request_info['fillterJewel']) ? $request_info['fillterJewel'] : "";
        $fillterMetalArray = (array) json_decode($request_info['fillterMetal'] ,true);
        $fillterJewelArray = (array) json_decode($request_info['fillterJewel'] ,true);
        $display_status = [  'term' => [   'display_status' => 1   ]  ];
        $size = 10; //每次显示10个商品
        $params_multi_match = [
            'index' => config("database.connections.elasticsearch.index"),
            'type'  => '_doc',
            "size" => $size,
            'from' => ($curPage-1) * $size,
            //ES的搜索逻辑是从第0个商品开始搜索的
            'body'  => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'terms' => [
                                'product_type' => self::$material[$keyword]
                            ]
                        ],
                        'must' => [
                            'term' => [
                                'display_status' => 1
                                //必须可展示状态才能被搜索
                            ]
                        ],
                    ]
                ],
                'track_scores' => true,
                '_source' => [
                    'product_id' , 'display_status' , 'product_detail',  'price' , 'product_name'
                ],
                'sort' => [
                    'product_id' => "asc"
                ],
                'post_filter' => [
                    'bool' => [
                        'must' => [
                            '0' => [
                                'range' => [
                                    'price' => [
                                        'gte'=> $fillterMinPrice ,
                                        'lte'=> $fillterMaxPrice
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ],
        ];

        //如果前端传了排序相关的2个字段，添加排序算法到ES请求中
        if (isset($request_info['sortKey']) && isset($request_info['sort'])){
            unset($params_multi_match['body']['sort']);

            $sort_filter = [ 1 => "asc", 2 => "desc"];
            $params_multi_match['body']['sort'][] = [$request_info['sortKey'] => $sort_filter[$request_info['sort']]];
            $params_multi_match['body']['sort'][] = ['product_id' => $sort_filter[$request_info['sort']]];
        }


        //如果传递了fillterCategory，代表用途类型，需要先聚簇后过滤
        if(isset($request_info['fillterCategory']) && $request_info['fillterCategory'] !== "all"){
            //特殊处理下手链脚链这个选项，同时传了B/M两个用途的值过来
            if($request_info['fillterCategory'] == "B/M"){
                $fillterCategory = [
                    'terms' => [
                        'usage_code' => ["B","M"]
                    ]
                ];
            }else{
                $fillterCategory = [
                    'term' => [
                        'usage_code' => $request_info['fillterCategory']
                    ]
                ];
            }


            $params_multi_match['body']['post_filter']['bool']['must'][] = $fillterCategory;
        }

        if ( sizeof($fillterMetalArray) >0){
            $fillterMetal = [
                'terms' =>
                    self::searchByFilterMetal($fillterMetalArray)
            ];
            $params_multi_match['body']['post_filter']['bool']['must'][] = $fillterMetal;

        }

        if ( sizeof($fillterJewelArray) >0){
            $fillterJewel = [
                'terms' =>
                    self::searchByFilterJewel($fillterJewelArray)

            ];
            $params_multi_match['body']['post_filter']['bool']['must'][] = $fillterJewel;

        }


        $params_multi_match['body']['aggs'] =  self::$filter['aggs'];
        $result = app('es')->search($params_multi_match);
        //这里渠道的结果不仅有匹配商品的Product Id，也有聚簇后的gold_type及product_type
        return $result;
    }


    public static function searchByFilterMetal($fillterMetalList)
    {
        $goldenTypeList = [];
        foreach ($fillterMetalList as $fillterMetal){
            $goldenTypeList[] = self::$filter["aggs"][$fillterMetal]["filter"]["bool"]["must"]["terms"]["gold_type_code"];
        }

        $newMetalList['gold_type_code'] = self::metalMultiToArray($goldenTypeList);
        return array_unique($newMetalList);
    }


    public static function searchByFilterJewel($fillterJewelList)
    {
        $productTypeList = [];
        foreach ($fillterJewelList as $fillterJewel){
            $productTypeList[] = self::$filter["aggs"][$fillterJewel]["filter"]["bool"]["must"]["terms"]['product_type'];
        }
        $newJewelList['product_type'] = self::jewelMultiToArray($productTypeList);
        return array_unique($newJewelList);
    }


    public static function metalMultiToArray($array) {
        static $result_array = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::metalMultiToArray($value);
            }
            else{
                $result_array[] = $value;
            }
        }
        return $result_array;
    }

    //从brandcode获取index
    public static function getIndexFromBrandCode($brand_code = ''){
        if(!$brand_code) $brand_code = Help::getBrandCode();
        return config("database.connections.elasticsearch.index");
//        return $cfg[trim(strtolower($brand_code))]??config("database.connections.elasticsearch.default_index");
//        if(!$brand_code) $brand_code = $_SERVER['brand-code']??'';
//        if(!$brand_code) return config("database.connections.elasticsearch.index");
//
//        $index = str_replace(' ','',strtolower($brand_code)).'-index';
//        return $index;

    }


    public static function jewelMultiToArray($array) {
        static $result_array = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::jewelMultiToArray($value);
            }
            else{
                $result_array[] = $value;
            }
        }
        return $result_array;
    }

    public static function makeupFilter($request,&$must,&$sort){
        //Dlc新增筛选
        $sort_column = array_get($request,'sort_column');
        $sort_direction = array_get($request,'sort_direction')?array_get(['asc'=>'asc','desc'=>'desc'],$request['sort_direction'])?:'asc':'asc';
        if($sort_column=='new'){//新品(商品更新时间降序，sort降序，商品ID降序)
            $sort = [
                ['updated_at' => 'desc'],
                ['sort' => 'desc'],
                ['unique_id' => 'desc'],
            ];
        }elseif($sort_column=='price'){//价格（价格高低，sort降序，商品更新时间降序，商品ID降序）
            $sort = [
                ['list_price'=>$sort_direction],
                ['sort' => 'desc'],
                ['updated_at' => 'desc'],
                ['unique_id' => 'desc'],
            ];
        }elseif($sort_column=='gift'){//礼盒 (是否为礼盒，sort降序，商品更新时间降序，商品ID降序)
            $must[]['term']['is_gift_box'] = 1;
            $sort = [
                ['sort' => 'desc'],
                ['updated_at' => 'desc'],
                ['unique_id' => 'desc'],
            ];
        }elseif($sort_column=='sales'){//销量排序
            $sort = [
                ['sales' => 'desc'],
                ['sort' => 'desc'],
                ['unique_id' => 'desc'],
            ];
        }else{//'hot'热门（sort降序，商品更新时间降序，商品ID降序）
            $sort = [
                ['sort' => 'desc'],
                ['updated_at' => 'desc'],
                ['unique_id' => 'desc'],
            ];
        }

        $filter_cat = array_get($request,'filter.cat');
        if($filter_cat){
            $must[]['nested'] = [
                'path'=>'cats_all',
                'query'=>['match'=>['cats_all.cat_id'=>$filter_cat]]
            ];
        }
        $filter_sub_cat = array_get($request,'filter.sub_cat');
        if($filter_sub_cat){
            $must[]['nested'] = [
                'path'=>'cats_all',
                'query'=>['match'=>['cats_all.cat_id'=>$filter_sub_cat]]
            ];
        }
        $filter_capacity_g = array_get($request,'filter.capacity_g');
        if($filter_capacity_g){
            $should = [];
            $filter_capacity_g_arr = explode(',',$filter_capacity_g);
            foreach($filter_capacity_g_arr as $item_g){
                $should[]['nested'] = [
                    'path'=>'specs_capacity_g',
                    'query'=>['match'=>['specs_capacity_g.value'=>$item_g]]
                ];
            }
            $must[]['bool']['should'] = $should;
        }
        $filter_capacity_ml = array_get($request,'filter.capacity_ml');
        if($filter_capacity_ml){
            $should = [];
            $filter_capacity_ml_arr = explode(',',$filter_capacity_ml);
            foreach($filter_capacity_ml_arr as $item_ml){
                $should[]['nested'] = [
                    'path'=>'specs_capacity_ml',
                    'query'=>['match'=>['specs_capacity_ml.value'=>$item_ml]]
                ];
            }
            $must[]['bool']['should'] = $should;
        }
        //选择价格区间
        $filter_option_price = array_get($request,'filter.option_price');
        if($filter_option_price){
            //获取价格区间的配置
            $option_price = config('common.option_price');
            $option_price = array_combine(array_column($option_price,'key'),$option_price);
            if(array_key_exists($filter_option_price,$option_price)){
                $price = $option_price[$filter_option_price]['condition'];
                $must[]['nested'] = [
                    'path'=>'all_price',
                    'query'=>['range'=>['all_price.value' => $price]]
                ];
            }
        }
        //输入价格区间
//        $filter_price = array_get($request,'filter.price');
//        if($filter_price){
//            $price_arr = explode(';',$filter_price??'');
//            $price['gte'] = $price_arr[0]?:'0.01';
//            $price['lte'] = $price_arr[1]??'99999999';
//            $must[] = ['range'=>['lowest_ori_price' => $price]];
//        }

    }

    public static function makeUpKeyWord($keyword){
        if($keyword){
            $keyword = strtoupper(str_replace('#', '', $keyword));
            return [
                'multi_match' => [
                    'query' => $keyword,
                    'type' => 'most_fields',
                    'fields' =>  [
                        'unique_id', //分类IDs
                        'tag',
                        'sku_ids',
                        'product_id',
                        'custom_keyword',        //自定义关键词
                        'product_name^3.5',           //匹配系列名称,权重第二高
                        'product_name_en^3.5',           //匹配系列名称,权重第二高
                        'list_name',
                        'cat_names',
                    ],
                ]
            ];
        }return [];
    }
}