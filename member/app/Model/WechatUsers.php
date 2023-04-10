<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WechatUsers extends Model
{
    protected $table = 'wechat_users';
   
    /**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
    protected $hidden = [
        'session_key',
    ];

    public function customers()
    {
        return $this->hasOne('App\Model\CrmCustomers', 'wechat_user_id', 'id');
    }


    public static function getUnionid($encryptedData, $iv, $openid)
    {
        $appid = config('app.appid');

        $wechatUser = WechatUsers::where('openid', $openid)->first();
        if(!$wechatUser) {
            return "openid 不合法";
        }
        $sessionKey = $wechatUser->session_key;

        $userinfo = new \WXBizDataCrypt($appid, $sessionKey);

        $errCode = $userinfo->decryptData($encryptedData, $iv, $data);
        if ($errCode == 0) {
            $info = json_decode($data, true);
            
            if(is_null($info)) { 
                return "解密unionid错误！";
            }

            logger('解密获取用户信息：', $info);

            $wechatUser->nickName  = base64_encode($info['nickName']);
            $wechatUser->avatarUrl = $info['avatarUrl'];
            $wechatUser->country   = $info['country'];
            $wechatUser->province  = $info['province'];
            $wechatUser->city      = $info['city'];
            $wechatUser->unionid   = array_key_exists('unionId', $info) ? $info['unionId'] : '';
            $wechatUser->gender    = $info['gender'];
            $wechatUser->authorize_at    = date('Y-m-d H:i:s');
            $wechatUser->save();
        
       
            if(array_key_exists('unionId', $info)) {
                return ['unionid' => $info['unionId']];
            } else {
                return ['unionid' => ''];
            }

        } else if($errCode == '-41003') {
            return '不合法的 session_key';

        } else {
            return '解密unionid错误！';

        }  
    }

}
