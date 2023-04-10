<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Repositories\{CmsRepository};
use App\Exceptions\ApiPlaintextException;

class CmsController extends ApiController
{
    public function getHomeList(Request $request){
        $content = CmsRepository::getBlock('cms_home_list');
        if($content){
            $list = json_decode($content,true);
        }else{
            $list = [];
        }
        return $this->success(compact('list'));
    }

}
