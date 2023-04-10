<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/1/13
 * Time: 10:43
 */

namespace App\Http\Controllers\Api\Search;
use App\Model\Goods\Blacklist;
use App\Service\Goods\SearchService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use PHPMailer\PHPMailer\Exception;

class ManageController extends Controller
{
    public function addBlackList(Request $request){
        $word = $request->word;
        if(!$word){
            return $this->error("参数缺失");
        }

        $info = [
            'word'=>$request->word
        ];
        $id = Blacklist::firstOrCreate($info,$info);
        if($id){
            SearchService::setBlackList($word);
        }
        return $this->success(['word'=>$word],"设置成功");

    }

    public function delBlackList(Request $request){
        $word = $request->word;
        if(!$word){
            return $this->error("参数缺失");
        }

        $id = Blacklist::where('word',$word)->delete();
        if($id){
            SearchService::delBlackList($word);
        }
        return $this->success(['word'=>$word],"设置成功");
    }

    /**
     * 获取搜索黑名单列表
     */
    public function blacklist(Request $request)
    {

        $limit = $request->limit ?: 10;
        $query = new Blacklist();
        if ($request->word) {
            $query = $query->where('word', $request->word);
        }

        $deProdData = $query->orderBy('id','desc')->paginate($limit)->toArray();
        $return = [];
        $return['pageData'] = $deProdData['data'];
        $return['count'] = $deProdData['total'];

        return $this->success($return);
    }

}