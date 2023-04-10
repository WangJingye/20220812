<?php

namespace App\Http\Controllers\Backend\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use App\Model\Config;
use App\Model\RedisModel;
use Illuminate\Support\Facades\DB;

class RecommendController extends Controller
{
//    public $recommendConfigDBName = ['pdp_rec','empty_cart_rec','search_rec','paid_rec'];
    public $recommendConfigDBName = ['pdp_rec','empty_cart_rec','search_rec','paid_rec','selected_rec'];
    public $recommendOption = [
        '1' => '用户最近浏览的前6个产品',
//        '2' => '同Usage下销量最高的前6个产品',
//        '3' => '同Usage下浏览量最高的前6个产品',
//        '4' => '用户最近收藏的前6个产品',
//        '5' => '最新上架的6个产品',
//        '6' => '自定义类目商品',
        '7' => '默认Sort升序6个',
        '8' => '默认Sort降序6个',
        '9' => '自定义商品',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = Config::whereIn('config_name', $this->recommendConfigDBName)->get()->toArray();

        $data = array_combine(array_column($data,'config_name'),$data);
        foreach($this->recommendConfigDBName as $rec){
            $data[$rec]['config_value'] = $data[$rec]['config_value']??1;
        }
        foreach($data as &$one){
            if(!empty($one['extension'])) $one['extension'] = json_decode($one['extension'],true);
        }

        return view('backend.config.recommend', ['recs'=>$this->recommendConfigDBName, 'data' => $data,'opts'=>$this->recommendOption]);
    }

    public function save(Request $request)
    {
        $postData = $request->all();

        if(!in_array($postData['config_name']??'',$this->recommendConfigDBName)) return ['code' => 0, 'msg' => '配置字段非法'];

        $update = [
            'config_name' => $postData['config_name'],
            'config_value' => $postData['config_value'],
            'extension' => json_encode($postData['extension'])
        ];
        try {
            Config::updateOrCreate(
                ['config_name' => $postData['config_name']],
                $update
            );

            return ['code' => 1];
        } catch (Exception $e) {
            echo $e->getMessage();

            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
}
