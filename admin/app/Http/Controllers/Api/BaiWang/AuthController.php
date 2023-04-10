<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/11/27
 * Time: 11:20
 */

namespace App\Http\Controllers\Api\BaiWang;

use App\Service\BaiWangAuthApi;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
    }

    /**
     * 获取百旺token
     * @param Request $request
     * @return mixed
     */
    public function getAccessToken(Request $request)
    {
        $appKey = $request->get('appKey');
        $token = BaiWangAuthApi::getInstance()->getAccessToken($appKey);

        return $this->success(['accessToken' => $token]);
    }
}