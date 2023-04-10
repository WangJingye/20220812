<?php

namespace App\Http\Controllers\Api\Advert;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\Guide\WechatHelpService;
use App\Service\Guide\EmployeeService;
use App\Model\EmployeeCode;
use App\Model\EmployeeMember;

class GuideController extends Controller
{
    protected $_model = EmployeeCode::class;

    /**
     * 获取推广二维码.
     */
    public function getGuideMiniCode(Request $request)
    {
        $return = [];
        $path = $request->path;
        $guide_code = $request->guide_code ?? 0;
        $sku_id = $request->sku_id  ?? '';
        if (empty($guide_code)) {
            return $this->error(2101, 'advert.guide.get_mini_code.path_or_json.required');
        }
        //查询当前导购是否是已经离职 或者是否是导购
        $sa_info = \DB::table('sa_list')->where(['sid'=>$guide_code,'status'=>1])->get()->toArray()?? 0;
        if($sa_info)
        {
            $id = EmployeeCode::insertGetId([
                'guide_code' => $guide_code,
                'store_code' => $sa_info[0]->store_id,
                'sku_id'     => $sku_id,
                'created_at'=>date('Y-m-d,H:i:s',time()),
                'updated_at'=>date('Y-m-d,H:i:s',time()),
            ]);
            $id_string = '&e='.$id;
            $return['e'] = $id;
        }
        else
        {
            $id = EmployeeMember::insertGetId([
                'empid' => $guide_code,
                'sku_id'     => $sku_id,
                'created_at'=>date('Y-m-d,H:i:s',time()),
                'updated_at'=>date('Y-m-d,H:i:s',time()),
            ]);
            $id_string = '&u='.$id;
            $return['u'] = $id;
        }
        if($id == false){
            return $this->error(2102,'advert.guide.share.fail');
        }
        $WechatHelpService = new WechatHelpService();
        $qrReturn = $WechatHelpService->generateQrCode('?code='.$sku_id.$id_string, $path, '1280', true);
        if($qrReturn['code'] == 1)
        {
            $ossClient = new \App\Lib\Oss();
            $remoteFilePath = 'miniStore/ba/qr-code/' . urlencode($qrReturn['fileName']);
            $ossBack = $ossClient->upload($remoteFilePath, $qrReturn['path']);
            if (true === $ossBack) {
                $path = env('OSS_DOMAIN').'/'. $remoteFilePath;
                $return['image'] = $path;
                return $this->success('成功',$return);
            }else{
                return $this->error(2104,'advert.guide.oss.fail');
            }
        }
        else {
            return $this->error($qrReturn['code'],$qrReturn['data']);
        }
    }

    /**
     * Notes:解析太阳码
     * User: 北辰兰绪
     * Email: Ming.Zhou@connext.com.cn
     * Date: 2020/3/18
     * Time: 13:59
     * @param Request $request
     * @return array|false|string
     */
    public function getGuideInfo(Request $request){
        $shareid = $request->shareId;

        if (empty($shareid)) {
            return $this->error(2105, 'advert.guide.get_mini_code.shareid.required');
        }
        $res = EmployeeCode::orWhere('id', $shareid)->orWhere('share_id',$shareid)->get()->toArray();

        if(!empty($res)){
            $return = ['json'=>$res[0]['data'],'time'=>$res[0]['created_at']];

            return $this->success($return);
        }else{
            return $this->error(2106, 'advert.guide.share.not_found');
        }
    }

    /**
     * Notes:获取分享id
     * User: 北辰兰绪
     * Email: Ming.Zhou@connext.com.cn
     * Date: 2020/4/20
     * Time: 11:16
     * @param Request $request
     * @return array
     */
    public function getGuideMini(Request $request){
        $shareid = $request->shareId;
        $json = $request->json;
        if (empty($json)) {
            return $this->error(2101, 'advert.guide.get_mini_code.path_or_json.required');
        }
        $id = EmployeeCode::insertGetId([
            'data'=>$json,
            'share_id'=>$shareid,
            'created_at'=>date('Y-m-d,H:i:s',time()),
            'updated_at'=>date('Y-m-d,H:i:s',time()),
        ]);

        if($id == false){
            return $this->error(2102,'advert.guide.share.fail');
        }else{
            return $this->success('返回成功');
        }
    }

    public  function getGuideService()
    {
        $info = (new EmployeeService())->getGuideInfo(request()->get('user_id'));
        return $this->success('返回成功',$info);

    }
}
