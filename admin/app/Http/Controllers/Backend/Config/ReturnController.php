<?php

namespace App\Http\Controllers\Backend\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use App\Model\Config;
use App\Model\RedisModel;

class ReturnController extends Controller
{
    public $returnConfigDBName = 'return';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = Config::where('config_name', $this->returnConfigDBName)->get()->toArray();

        $detail = [];
        $detail['image'] = '';
        if (!empty($data)) {
            foreach ($data as $item) {
                if ($this->returnConfigDBName == $item['config_name']) {
                    $detail['image'] = $item['config_value'];
                }
            }
        }

        return view('backend.config.return', ['detail' => array_to_object($detail)]);
    }

    public function save(Request $request)
    {
        $postData = $request->all();
        $update = [
            'config_name' => $this->returnConfigDBName,
            'config_value' => $postData['image'],
        ];
        try {
            Config::updateOrCreate(
                ['config_name' => $this->returnConfigDBName],
                $update
            );

            $RedisModel = new RedisModel();
            $RedisModel->_setDb(6);
            $RedisModel->_hset(config('redis.sysConfig'), $this->returnConfigDBName, $postData['image']);

            return ['code' => 1];
        } catch (Exception $e) {
            echo $e->getMessage();

            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }
}
