<?php  

namespace App\Model;

use Illuminate\Support\Facades\Redis;

trait ModelTrait
{
	/**
     * 获取Token
     *
     * @author Lzz
     * @date   2019-09-26
     * @param  string	$key	Redis中键值
     * @return string
     */
    public static function getToken($encryptString)
    {
        $decryptedData = decrypt($encryptString);

        if(!array_key_exists('wechatUserId', $decryptedData)) {
            return false;
        }
        if(empty($decryptedData['crmId'])) {  //为空，匿名登录
            return 3; // 登录
        }
        $key = $decryptedData['wechatUserId'];

		$encryptStr = Redis::get($key);

        if(empty($encryptStr)) {
            return 2; // 重新登录
        }

        $extra = $encryptString === $encryptStr ? true : false;
        if($extra) {
            Redis::expire($key, 7*24*3600);
            return true;
        } 
        return false;
		
    }

    /**
     * 获取getLoginToken
     *
     * @author Lzz
     * @date   2019-09-26
     * @param  string   $key    Redis中键值
     * @return string
     */
    public static function getLoginToken($wechatUserId, $openId, $crmId)
    {
        $token = Redis::get($wechatUserId);
        if(!$token || $crmId) {
            $token = self::setToken($wechatUserId, $openId, $crmId);
        }
        if($token && $crmId) {
            Redis::expire($wechatUserId, 7*24*3600);
        }
        return $token;
        
    }

    /**
     * 设置Token
     *
     * @author Lzz
     * @date   2019-09-26
     * @param  int		$wechatUserId		用户id
     * @param  string	$openId		用户唯一标示
     * @param  int		$crmId		crmId
     * @return string
     */
    public static function setToken($wechatUserId, $openId, $crmId)
    {
		$data = [
		    'wechatUserId' => $wechatUserId,
		    'openId' => $openId,
		    'crmId'  => $crmId,
		    'createTime' => time()
		];

		//加密
		$encrypted = encrypt($data);

        if($crmId){
            Redis::setex($wechatUserId, 7*24*3600, $encrypted);
        } else {
            Redis::set($wechatUserId, $encrypted);
        }
		return $encrypted;
    }
}