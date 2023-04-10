<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/11/4
 * Time: 9:53
 */

namespace App\Model\Search;

use App\Model\Help;
use Illuminate\Support\Facades\Redis;
use App\Model\Redis as RedisM;
class Redirect extends Model
{

    /**
     * 匹配重定向关键词，用包含方式匹配。
     * @param $keywords
     * @return array|string
     */
    public static function matchRedirectKeyword($keywords)
    {
        $brand = Help::getBrandCode();
        $redis = RedisM::getRedis('cmskeyword');
        $key = RedisM::getKey('rk.search.redirect',['brand'=>$brand]);
        $redirectKeywordList = $redis->hkeys($key);
        //读取配置代码中的重定向关键词列表
        //先做精准判断，关键词是否在重定向关键词列表中，匹配到了即可返回组合结果
        if (in_array($keywords, $redirectKeywordList)){
            $code = $redis->hget($key, $keywords);
            $result = self::packRedirectInfo($code);
            return $result;
        }

        //没精准匹配到，则做模糊匹配，匹配到了也即可返回。
        foreach ($redirectKeywordList as $redirectKeyword){
            $result = [];
            //这里主要解决一个Bug，今天遇到的Redis这个Hash里居然有空值键名，导致bug
            if($redirectKeyword !=null){
                //判断重定向关键词是否出现在用户输入的搜索关键词中，如不存在结果是false
                //这里有个肯点，就是第一位就匹配到会返回结果0，所以需要使用!==来判断
                if(strpos($keywords, $redirectKeyword) !== false){
                    $code = $redis->hget($key, $redirectKeyword);
                    $result = self::packRedirectInfo($code);
                    return $result;
                }
            }


        }

        return "false";
    }

    /**
     * 手动初始化一些搜索跳转关键词
     */
    public static function setRedirectKeywordsToRedis()
    {
        $brand = Help::getBrandCode();
        $redis = RedisM::getRedis('cmskeyword');
        $key = RedisM::getKey('rk.search.redirect',['brand'=>$brand]);
        return "done";
    }

    public static function packRedirectInfo($code){
        $path = "/pages/default/landing/landing?code=$code";
        //如果code是1，就是首页，页面地址需要变化
        if($code === "1"){
            $path = "/pages/default/home/home?code=$code";
        }
        $result["list"]  = array();
        $result["totalPage"]  = "1";
        $result["curPage"]    = "1";
        $result["isFillterPrice"] = false;
        $result["action"]["type"] = "inner_link";
        $result["action"]["data"]["path"] = $path;
        $result["action"]["data"]["type"] = "navigateTo";
        return $result;

    }


}