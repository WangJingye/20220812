<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/8/11
 * Time: 14:37
 */

namespace App\Model\Skin;

use App\Model\Skin\SkinReportModel as SkinReportModel;

class SkinReportData
{
    public function makeSkinReportData($fiveAgeData, $fiveGloss, $fiveStains, $fiveDarkCirclesData, $fiveWrinkleData)
    {
        
    }

    public function reportList($openid){
        $skinReportModel = new SkinReportModel();
        $data = [
            'openid' => $openid,
//            'id' => 1
        ];
        $report_data = $skinReportModel::where($data)->where('answer_time','!=','')->get()->toArray();

        return $report_data;
    }

    public function reportDetail($openid, $report_id)
    {
        $skinReportModel = new SkinReportModel();
        $data = [
            'openid' => $openid,
            'id' => $report_id
        ];
        $report_data = $skinReportModel::where($data)->get()->toArray();
        return $report_data;
    }

    /**
     * 更新用户上传照片后的原始报告+五维图信息到DB中
     * @param $openid
     * @param $original_report_url
     * @param $five_data
     * @param $skin_image_url
     * @return mixed
     */
    public function reportToDB($openid, $original_report_url, $five_data, $skin_image_url){
        $skinReportModel = new SkinReportModel();
        $data = [
            'openid' => $openid,
            'original_report_url' => $original_report_url,
            'fiveAgeData' => $five_data['fiveAgeData'],
            'fiveGloss' => $five_data['fiveGloss'],
            'fiveStains' => $five_data['fiveStains'],
            'fiveDarkCirclesData' => $five_data['fiveDarkCirclesData'],
            'fiveWrinkleData' => $five_data['fiveWrinkleData'],
            'skin_image_url' => $skin_image_url,
            'created_at' => date("Y-m-d H:i:s")
        ];

        $where = [
            'openid' => $openid,
            'original_report_url' => $original_report_url
        ];

       return $skinReportModel::updateOrCreate($where, $data);

    }

    public function answerToDB($openid, $report_id,$answer)
    {
        $skinReportModel = new SkinReportModel();
        $data = [
            'openid' => $openid,
            'id' => $report_id,
            'q1' => $answer['q1'],
            'q2' => $answer['q2'],
            'q3' => $answer['q3'],
            'q4' => $answer['q4'],
            'answer_time' => date("Y-m-d H:i:s"),
        ];

        $where = [
            'openid' => $openid,
            'id' => $report_id,
        ];
        return $skinReportModel::updateOrCreate($where, $data);
    }
}