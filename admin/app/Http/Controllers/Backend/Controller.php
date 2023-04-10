<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Lib\Http;
use App\Model\Permission;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $_http;
    protected $pageSize;

    public function __construct()
    {
        $this->middleware('auth');
        $this->_http = new Http();
        $this->pageSize = config('view.page_size');
    }

    /**
     * @param string $url
     * @param array  $data
     *
     * @return mixed
     */
    public function curl($apiName, $data = [])
    {
        return $this->_http->curl($apiName, $data);
    }

    public function tree()
    {
        $response = $this->curl('category/tree');

        return $response;
    }

    /**
     * 处理权限分类.
     */
    public function permissionTree($list = [], $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0)
    {
        if (empty($list)) {
            $list = Permission::get()->toArray();
        }
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }

        return $tree;
    }

    public function success($data, $message = 'ok')
    {
        return [
            'code' => 1,
            'data' => $data,
        ];
    }

    public function error($message = 'fail')
    {
        return [
            'code' => 0,
            'error' => [
                'code' => 500,
                'type' => 'NORMAL',
                'message' => 'fail',
            ],
        ];
    }

    public function responseJson($code = 0, $msg = '', $data = [] ,$total = 0)
    {

        return [
            'code'   => $code,
            'msg'    => $msg,
            'status' => $code == 200 ? 'success' : 'error',
            'count' => $total,
            'data'   => $data,

        ];

    }

    public function redirect($path, $type, $message)
    {
        request()->session()->flash($type, $message);

        return redirect($path);
    }


    public function alert($msg='qqq',$time=3){
        $str='<script type="text/javascript" src="../../../static/admin/js/jquery.min.js"></script><script type="text/javascript" src="../../../static/layer/layer.js"></script>';//加载jquery和layer
        $str.='<script>$(document).ready(function(){layer.alert("'.$msg.'");});</script>';//主要方法
        echo $str;die;
    }
}
