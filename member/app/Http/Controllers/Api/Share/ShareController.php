<?php namespace App\Http\Controllers\Api\Share;

use App\Exceptions\EventExpireException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use Validator;
use App\Service\Dlc\HelperService;
use App\Service\Share\ShareService;
use Illuminate\Support\Facades\Log;

/**
 * @author Steven
 * Class ShareController
 * @package App\Http\Controllers\Api\Share
 */
class ShareController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Log::info('ShareController',[
            'RequestUri'=>request()->getUri(),
            'RequestData'=>request()->all()
        ]);
    }

    /**
     * 建立关系
     * @param Request $request
     * @return mixed
     */
    public function bind(Request $request){
        try{
            $params = $request->all();
            $v = Validator::make($params, [
                'uid' => 'required',
                'openid' => 'required',
            ], [
                'uid.required' => '分享者ID不能为空',
                'openid.required' => 'OPENID不能为空',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $sharer_id_index = $params['uid'];
            $sharer_openid = HelperService::getOpenidByIndex($sharer_id_index);
            if($sharer_openid){
                $friend_openid = $params['openid'];
                $share_service = new ShareService;
                $data = $share_service->bindRelation($sharer_openid,$friend_openid);
            }
            return $this->success('OK',$data??[]);
        }catch (EventExpireException $e){
            return $this->eventExpire();
        }catch (\Exception $e){
            return $this->error();
        }
    }

    /**
     * 算人头回调(内部调用)
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function notify(Request $request){
        try{
            (new ShareService)->notify($request->all());
        }catch(\Exception $e){
            return $this->success($e->getMessage());
        }
        return $this->success();
    }

}