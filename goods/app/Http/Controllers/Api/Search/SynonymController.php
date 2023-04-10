<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/1/13
 * Time: 10:43
 */

namespace App\Http\Controllers\Api\Search;
use App\Model\Goods\Blacklist;
use App\Model\Goods\Synonym;
//use App\Service\SearchService;
use App\Service\Goods\SearchService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Validator;

class SynonymController extends Controller
{

    public function getAllSynonym(Request $request){
        $all = Synonym::all();
        return $this->success($all,"设置成功");
    }

    public function addSynonym(Request $request){

//        SearchService::updateRomoteSynonym();

        $all = $request->all();
        $fields = [
            'word' => 'required',
            'convert_word' => 'required',
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
            'convert_word'=>$request->convert_word,
            'remark'=>$request->remark,
        ];
        $id = Synonym::firstOrCreate(['convert_word'=>$request->convert_word],$info);
//        if($id){
//            SearchService::setSynonym($request->word,$request->type,$request->code);
//        }
        return $this->success(['word'=>$request->word],"设置成功");

    }

    public function updateSynonym(Request $request){

        $fields = [
            'word' => 'required',
            'id' => 'required',
//            'old_word' => 'required',
            'convert_word' => 'required',
//            'old_convert_word' => 'required',
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

//        if(($request->old_word == $request->word) && ($request->convert_word == $request->old_convert_word))  SearchService::delSynonym($request->old_word);

        $info = [
            'word'=>$request->word,
            'convert_word'=>$request->convert_word,
            'remark'=>$request->remark
        ];
        $id = Synonym::where('id',$request->id)->update($info);
        if($id){
//            if($request->old_word != $request->word)  SearchService::delSynonym($request->old_word);
//            SearchService::setSynonym($request->word,$request->type,$request->code);
        }
        return $this->success(['word'=>$request->word],"设置成功");

    }

    public function delSynonym(Request $request){
        $id = $request->id;
        if(!$id){
            return $this->error("参数缺失");
        }

        $id = Synonym::where('id',$id)->delete();
        if($id){
//            SearchService::delSynonym($word);
        }
        return $this->success(['id'=>$id],"设置成功");
    }

    /**
     * 获取搜索黑名单列表
     */
    public function synonymList(Request $request)
    {

        $limit = $request->limit ?: 10;
        $query = new Synonym();
        $word = $request->word;
        if ($request->word) {
            $query = $query->where(function ($query) use ($word) {
                $query
                    ->orWhere('word', 'like', "%{$word}%")
                    ->orWhere('convert_word', 'like', "%{$word}%");
            });
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
    public function getSynonymInfo(Request $request)
    {
        $id = $request->id ?: 0;
        if(!$id) return $this->error('参数缺失');
        $query = new Synonym();
        $rec = $query->where('id', $id)->first();
        if($rec) return $this->success($rec->toArray());
        return $this->error('未查询到');
    }


}