<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Common\YouShu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Users extends Model
{

    protected $table = 'tb_users';
//    public $timestamps = true;
//    protected $fillable = ['pos_id', 'phone', 'email','password','respect_type','nickname','name','birth','sex'];
    protected $guarded = ['id'];

    const YOUSHU_API = [
        1 => 'https://test.zhls.qq.com/data-api/v1/user/add_user',
        2 => 'https://test.zhls.qq.com/data-api/v1/analysis/add_wxapp_visit_page'
    ];
    
    public function hasManySocialUsers()
    {
        return $this->hasMany('App\Model\SocialUsers', 'user_id', 'id');
    }

    //获取用户收藏
    public static function getUserInfo($user_id){
        $data = self::onWriteConnection()->where('id',$user_id)->first();
        return json_decode(json_encode($data),true);
    }

    public function guide(){
        return $this->hasOne("App\Model\SaList",'sid','guid_id');
    }

    /**
     * 添加用户信息
     */
    public static function taskAddUser($id)
    {
        $sign = YouShu::getReqSign();
        //查询user信息
        $user = DB::table('tb_users')
                            ->where('id', $id)
                            ->first();
        $result = [];
        if (!empty($user)) {
            $param = json_encode([
                "dataSourceId"  => "10904",
                "users"         => [[
                    "user_id"       => (string) $user->id,
                    "phone_number"  => !empty($user->phone) ? (string) $user->phone : '空',
                    "user_spec"     => [],
                    "basic_spec"    => [
                        "name"          => !empty($user->name) ? $user->name : '空',
                        "nickname"      => !empty($user->nickname) ? $user->nickname : '空',
                        "header_url"    => !empty($user->pic) ? $user->pic : '空',
                        "gender"        => $user->sex == 'M' ? 1 : 2
                    ],
                ]],
            ], true);
            //请求有数api
            $url = self::YOUSHU_API[1] . '?' . $sign;
            $http = new YouShu();
            $result = $http->curl_post($url, $param);
            Log::info('taskAddUser:'.json_encode($result, true));
        }
        return $result;
    }

    /**
     * 导入user（有数）历史数据
     */
    public static function exportUserHistory()
    {
        $result = [];
        $sign = YouShu::getReqSign();
        //查询user信息
        DB::table('tb_users')
            ->where('channel', 1)
            ->orderBy('id', 'ASC')
            ->chunk(100, function($userInfo) use ($sign){
                if (!empty($userInfo)) {
                    $users = [];
                    foreach ($userInfo as $k=>$user) {
                        $users[] = [
                            "user_id"       => (string) $user->id,
                            "phone_number"  => !empty($user->phone) ? (string) $user->phone : '空',
                            "user_spec"     => [],
                            "basic_spec"    => [
                                "name"          => !empty($user->name) ? $user->name : '空',
                                "nickname"      => !empty($user->nickname) ? $user->nickname : '空',
                                "header_url"    => !empty($user->pic) ? $user->pic : '空',
                                "gender"        => $user->sex == 'M' ? 1 : 2
                            ],
                        ];
                    }
                    $param = json_encode([
                        "dataSourceId"  => "10904",
                        "users"         => $users,
                    ], true);
                    //请求有数api
                    $url = self::YOUSHU_API[1] . '?' . $sign;
                    $http = new YouShu();
                    $result = $http->curl_post($url, $param);
                    Log::info('exportUserHistory:'.json_encode($result, true));
                }
            });
        return $result;
    }

    /**
     * 上报页面访问
     */
    public static function addWxappVisitPage()
    {
        $wxVisit = YouShu::wxVisit();
        $sign = YouShu::getReqSign();
        $list = [];
        if (!empty($wxVisit['list'])) {
            foreach ($wxVisit['list'] as $k=>$v) {
                $list[] = [
                    'page_path'         => !empty($v['page_path']) ? (string) $v['page_path'] : '空',
                    'page_visit_pv'     => !empty($v['page_visit_pv']) ? (int) $v['page_visit_pv'] : 0,
                    'page_visit_uv'     => !empty($v['page_visit_uv']) ? (int) $v['page_visit_uv'] : 0,
                    'page_staytime_pv'  => !empty($v['page_staytime_pv']) ? (float) $v['page_staytime_pv'] : 0,
                    'entrypage_pv'      => !empty($v['entrypage_pv']) ? (int) $v['entrypage_pv'] : 0,
                    'exitpage_pv'       => !empty($v['exitpage_pv']) ? (int) $v['exitpage_pv'] : 0,
                    'page_share_pv'     => !empty($v['page_share_pv']) ? (int) $v['page_share_pv'] : 0,
                    'page_share_uv'     => !empty($v['page_share_uv']) ? (int) $v['page_share_uv'] : 0,
                ];
            }
        }
        $param = json_encode([
            'dataSourceId'  => '10907',
            'rawMsg'        => [[
                'ref_date'  => !empty($wxVisit['ref_date']) ? $wxVisit['ref_date'] : '空',
                'list'      => $list
            ]]
        ], true);
        //请求有数api
        $url = self::YOUSHU_API[2] . '?' . $sign;
        $http = new YouShu();
        $result = $http->curl_post($url, $param);
        Log::info('addWxappVisitPage:'.json_encode($result, true));
        return $result;
    }

}
