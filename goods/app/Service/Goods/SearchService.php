<?php

namespace App\Service\Goods;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use App\Lib\Oss;
use App\Model\Ad\Item;
use App\Model\Ad\Location;
use App\Model\Goods\Blacklist;
use App\Model\Goods\Redirect;
use App\Model\Goods\Synonym;
use App\Model\Help;
use App\Service\ServiceCommon;
use Illuminate\Support\Facades\Redis;
use App\Model\Redis as RedisM;

class SearchService extends ServiceCommon
{
    const Black_List_Key = 'search:blacklist';
    const Synonym_List_Key = 'search:synonym';
    /**
     * 匹配违禁词、如匹配则停止搜索返回无结果
     * @param $keywords
     * @return mixed
     */
    public static function matchBlackList($keywords)
    {
        $isBlack = Redis::sismember(config('app.name').':'.self::Black_List_Key, $keywords);
        $isEmoji = Help::have_special_char($keywords);
        if($isEmoji){
            $isBlack = true;
        }
        return $isBlack;
    }

    /**
     * 设置违禁词信息
     * @param $keywords
     * @return mixed
     */
    public static function setBlackList($keyword){
        Redis::sadd(config('app.name').':'.self::Black_List_Key, $keyword);
        return Redis::smembers(config('app.name').':'.self::Black_List_Key);

    }

    //缓存所有黑名单
    public static function cacheAllBlackList(){
        $redis = ProductService::getRedis();
        $list = Blacklist::query()->pluck('word');
        $list = $list->toArray();
        if(count($list)){
            $redis->sadd(config('app.name').':'.self::Black_List_Key,$list);
            $keys = $redis->smembers(config('app.name').':'.self::Black_List_Key);
            $diff = array_diff($keys,$list);
            if($diff){
                $redis->srem(config('app.name').':'.self::Black_List_Key,$diff);
            }
        }else{
            $redis->del(config('app.name').':'.self::Black_List_Key);
        }return true;
    }

    public static function convertSynonym(&$keyword)
    {
        $redis = ProductService::getRedis();
        $word = $redis->hget(self::Synonym_List_Key,$keyword);
        $keyword = $word?:$keyword;
    }

    public static function cacheAllSynonym(){
        $redis = ProductService::getRedis();
        $list = Synonym::query()->pluck('convert_word','word');
        $list = $list->toArray();
        if(count($list)){
            $redis->hmset(self::Synonym_List_Key,$list);
            $keys = $redis->hgetall(self::Synonym_List_Key);
            $diff = array_diff_key($keys,$list);
            if($diff){
                $redis->hdel(self::Synonym_List_Key,array_keys($diff));
            }
        }else{
            $redis->del([self::Synonym_List_Key]);
        }return true;
    }

    //缓存所有关键字跳转
    public static function cacheAllRedirect(){
        $brand = Help::getBrandCode();
        $pre_id = 0;
        $tmp_key = date('YmdHis').$brand;
        $RedisModel = ProductService::getRedis();
        $model = new Redirect();
        $key = RedisM::getKey('rk.search.redirect',['brand'=>$brand]);
        $cache = [];

        while(true){
//            $params['id'] = ['gt',$pre_id];
            $records = $model->where('id','>',$pre_id)
                ->limit(100)->get();
            if($records->isEmpty()) break;
            $records = $records->toArray();
            foreach ($records as $rec){
                $pre_id = max($pre_id,$rec['id']);
                $cache[$rec['word']] = json_encode(['type'=>$rec['type'],'code'=>$rec['code']]);
            }
        }
        if($cache){
            $RedisModel->hmset($tmp_key,$cache);
            $RedisModel->rename($tmp_key,$key);
        }

        return $cache;
    }

    public static function getBlackList(){
        $brand = Help::getBrandCode();
        $redis = RedisM::getRedis('statistics');
        $key = RedisM::getKey('rk.search.blacklist',['brand'=>$brand]);
        return $redis->Smembers($key);
    }

    /**
     * 删除违禁词信息
     * @param $keywords
     * @return mixed
     */
    public static function delBlackList($keywords){
        $brand = Help::getBrandCode();
        $redis = RedisM::getRedis('statistics');
        $key = RedisM::getKey('rk.search.blacklist',['brand'=>$brand]);
//        $redis = app('redis.connection');
        $blacklist_array = explode(" ",$keywords);
        foreach ($blacklist_array as    $blacklist_detail){
            $redis->Srem($key, $blacklist_detail);
        }
//        $redis->Sadd(config('app.name') . "_BlackList", "I DO");
//        $redis->Sadd(config('app.name') . "_BlackList", "darry ring");
        return true;

    }

    /**
     * 设置跳转
     * @param $keywords
     * @return mixed
     */
    public static function setRedirect($word,$type,$code){
        $brand = Help::getBrandCode();
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.search.redirect',['brand'=>$brand]);

        $info = [
            'type'=>$type,
            'code'=>$code,
        ];
        $redis->hset($key,$word,json_encode($info));
        return true;
    }

    /**
     * 设置跳转
     * @param $keywords
     * @return mixed
     */
    public static function delRedirect($word){
        $brand = Help::getBrandCode();
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.search.redirect',['brand'=>$brand]);

        $redis->hdel($key,$word);
        return true;
    }

    /**
     * 匹配重定向关键词，用包含方式匹配。
     * @param $keywords
     * @return array|string
     */
    public static function matchRedirectKeyword($keywords)
    {
        $brand = Help::getBrandCode();
        $redis = RedisM::getRedis('statistics');
        $key = RedisM::getKey('rk.search.redirect',['brand'=>$brand]);
        $redirectKeywordList = $redis->hkeys($key);
        $pService = new ProductService();
        //读取配置代码中的重定向关键词列表
        //先做精准判断，关键词是否在重定向关键词列表中，匹配到了即可返回组合结果
        if (in_array($keywords, $redirectKeywordList)){
            $redirect_info = $redis->hget($key, $keywords);
            $redirect_info = json_decode($redirect_info,true);
            if(!$redirect_info) return false;

            //合法性校验
            switch ($redirect_info['type']){
                case 1:
                    $info = $pService->getProductInfoFromCache($redirect_info['code']);
                    if(empty($info['status'])) return false;
                    break;
                case 2:
                    $cat = $pService->getCatInfoFromCache($redirect_info['code']);
                    if(empty($cat['status'])) return false;
                    break;
            }

            $result = self::packRedirectInfo($redirect_info);
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


    const REDIRECT_MAP = [
        1=>'product',
        2=>'cat',
        3=>'project'
    ];
    public static function packRedirectInfo($redirect_info){
//        $path = "/pages/default/landing/landing?code=$code";
        //如果code是1，就是首页，页面地址需要变化
//        if($code === "1"){
//            $path = "/pages/default/home/home?code=$code";
//        }
        $result["list"]  = array();
        $result["totalPage"]  = "1";
        $result["curPage"]    = "1";
        $result["type"] = self::REDIRECT_MAP[$redirect_info['type']]; //search 搜索列表  product 直跳商品 cat 直跳类目 project 直跳专题页
        $result["code"] = $redirect_info['code'];
        return $result;

    }

    public static function setHotKeyword($keywords){
        $brand = Help::getBrandCode();
        $Statistics_Redis = RedisM::getRedis('statistics');
        $key = RedisM::getKey('rk.search.hot_keywords',['date'=>date("Ymd"),'brand'=>$brand]);
        $Statistics_Redis->zincrby($key, 1 ,$keywords);
        return true;
    }

    public static function getTopHotKeyword($num = 10){
        $brand = Help::getBrandCode();
        $Statistics_Redis = RedisM::getRedis('statistics');

        $key = RedisM::getKey('rk.search.hot_keywords',['date'=>date("Ymd",strtotime('-1 day')),'brand'=>$brand]);
        return $Statistics_Redis->zrange($key, 0 ,$num);
    }

    public static function updateRomoteSynonym(){
        $brand = Help::getBrandCode();
        $all = Synonym::all();
        $content = '';
        foreach($all as $one){
            $content .= $one['word'].','.$one['convert_word'].PHP_EOL;
        }

//        $local_path = 'C:\Users\Pompey.Guang\Pictures\Saved Pictures\108310_01.jpg';
        $local_path = 'D:\workdata\log\syno_'.$brand.'.txt';
        file_put_contents($local_path,$content);
//
        $path = 'es/syno_'.$brand.'.txt';
////        $path = '/'.$brand.'/es/synonym.txt';
        $oss = new Oss();
        $oss->upload($path,$local_path);

        AlibabaCloud::accessKeyClient('LTAI4GDvrNEYjNLwWZ9d9sG1', 'FNlePRbiMxvrll0uFVeZYF4CD1yz6L')
            ->regionId('cn-hangzhou')
            ->asDefaultClient();

        try {
            $result = AlibabaCloud::roa()
                ->product('elasticsearch')
                // ->scheme('https') // https | http
                ->version('2017-06-13')
                ->pathPattern('/openapi/instances/es-cn-nif1psupl000czviq/synonymsDict')
                ->method('PUT')
                ->options([
                    'query' => [

                    ],
                ])
                ->body('[
    {
        "name": "deploy_0.txt",
        "ossObject": {
            "bucketName": "buck-oss-gpj-es",
            "key": "es/tyc_0.txt"
        },
        "sourceType": "OSS",
        "type": "SYNONYMS"
    }
]')

                ->request();
            echo '[
    {
        "name": "syno_'.$brand.'.txt",
        "ossObject": {
            "bucketName": "buck-oss-gpj-es",
            "key": "'.$path.'"
        },
        "sourceType": "OSS",
        "type": "SYNONYMS"
    }
]';
            print_r($result->toArray());
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }


    }

}
