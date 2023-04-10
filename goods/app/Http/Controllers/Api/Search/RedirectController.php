<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/1/13
 * Time: 10:43
 */

namespace App\Http\Controllers\Api\Search;
use App\Model\Goods\Blacklist;
use App\Model\Goods\Redirect;
//use App\Service\SearchService;
use App\Service\Goods\SearchService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Validator;

class RedirectController extends Controller
{

    public function addRedirect(Request $request){
        $all = $request->all();
        $fields = [
            'word' => 'required',
            'type' => 'required',
            'code' => 'required',
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
//            'string'     => ':attribute 为字符串',
//            'min'       => ':attribute至少:min位字符长度'
            ]
        );
        if($validator->fails()){
            return $this->error($validator->errors()->first());
        }

        $info = [
            'word'=>$request->word,
            'type'=>$request->type,
            'code'=>$request->code
        ];
        $id = Redirect::firstOrCreate(['word'=>$request->word],$info);
        if($id){
            SearchService::setRedirect($request->word,$request->type,$request->code);
        }
        return $this->success(['word'=>$request->word],"设置成功");

    }

    public function updateRedirect(Request $request){

        $fields = [
            'word' => 'required',
            'id' => 'required',
            'old_word' => 'required',
            'type' => 'integer',
            'code' => 'required',
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
//            'string'     => ':attribute 为字符串',
//            'min'       => ':attribute至少:min位字符长度'
            ]
        );
        if($validator->fails()){
            return $this->error($validator->errors()->first());
        }

        $info = [
            'word'=>$request->word,
            'type'=>$request->type,
            'code'=>$request->code
        ];
        $id = Redirect::where('id',$request->id)->update($info);
        if($id){
            if($request->old_word != $request->word)  SearchService::delRedirect($request->old_word);
            SearchService::setRedirect($request->word,$request->type,$request->code);
        }
        return $this->success(['word'=>$request->word],"设置成功");

    }

    public function delRedirect(Request $request){
        $word = $request->word;
        if(!$word){
            return $this->error("参数缺失");
        }

        $id = Redirect::where('word',$word)->delete();
        if($id){
            SearchService::delRedirect($word);
        }
        return $this->success(['word'=>$word],"设置成功");
    }

    /**
     * 获取搜索黑名单列表
     */
    public function redirectList(Request $request)
    {

        $limit = $request->limit ?: 10;
        $query = new Redirect();
        if ($request->word) {
            $query = $query->where('word', $request->word);
        }

        $deProdData = $query->orderBy('id','desc')->paginate($limit)->toArray();
        $return = [];
        $return['pageData'] = $deProdData['data'];
        $return['count'] = $deProdData['total'];

        return $this->success($return);
    }

    /**
     * 获取搜索黑名单列表
     */
    public function getRedirectInfo(Request $request)
    {
        $id = $request->id ?: 0;
        if(!$id) return $this->error('参数缺失');
        $query = new Redirect();
        $rec = $query->where('id', $id)->first();
        if($rec) return $this->success($rec->toArray());
        return $this->error('未查询到');
    }

}