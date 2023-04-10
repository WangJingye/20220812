<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/8/1
 * Time: 11:07
 */

namespace App\Http\Controllers\Api\Skin;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Http\Requests\SkinRequest;
use App\Http\Controllers\Api\Controller;
use App\Model\Skin\SkinAnalysis;
use App\Model\Skin\SkinReportData;
use App\Model\Skin\SkinRecommend;
use App\Model\Skin\SkinQuestion;

class SkinAnalysisController extends Controller
{
    public function miniSkinTest(Request $request){
        $open_id = $request->get("openid");
        $image_url = $request->get("image_url");
        $skinAnalysis = new SkinAnalysis();
        $skin_data = $skinAnalysis->skinTest($open_id, $image_url);
        //如果提前返回了错误值，那就直接接口报错返回了
        if(!is_array($skin_data)){
            return $this->error(0,"fail",  $skin_data);
        }
        else{
            return $this->success($skin_data);
        }
    }



    public function historyReportDetail(Request $request)
    {
        $openid = $request->get("openid");
        $report_id = $request->get("report_id");
        $skinReportData = new SkinReportData();
        $skinQuestion = new SkinQuestion();
        $skinRecommend = new SkinRecommend();
        $reportDetail = [];
        $result = $skinReportData->reportDetail($openid ,$report_id);
        if(sizeof($result) > 0){
            $resultDetail = $result[0];
            $answer['q1'] = $resultDetail['q1'];
            $answer['q2'] = $resultDetail['q2'];
            $answer['q3'] = $resultDetail['q3'];
            $answer['q4'] = $resultDetail['q4'];
            $reportDetail['report_id'] = $resultDetail['id'];
            $fiveSkinData['fiveAgeData'] = $resultDetail['fiveAgeData'];
            $fiveSkinData['fiveGloss'] = $resultDetail['fiveGloss'];
            $fiveSkinData['fiveStains'] = $resultDetail['fiveStains'];
            $fiveSkinData['fiveDarkCirclesData'] = $resultDetail['fiveDarkCirclesData'];
            $fiveSkinData['fiveWrinkleData'] = $resultDetail['fiveWrinkleData'];
            $reportDetail['five_images'] = $fiveSkinData;
            $reportDetail['problem'] = $skinQuestion->fiveProblemReport($fiveSkinData);
            $reportDetail['problemFields'] = $skinRecommend->problemFields($fiveSkinData);
            $reportDetail['answer'] = $skinRecommend->getAnswer($answer);
            $q4Product = $skinRecommend->getQ4Product($answer);
            $skinProduct = $skinRecommend->getSkinProduct($fiveSkinData);
            $productIdList = array_unique(array_merge($skinProduct,$q4Product));
            $reportDetail['product'] = $skinRecommend->getRecProduct($productIdList);
            $reportDetail['skincare_ritual'] = $skinRecommend->skincareRitual($answer);
            $reportDetail['report_date'] = date("Y年m月d日",strtotime($resultDetail['answer_time']));
            $reportDetail['skin_image_url'] = $resultDetail['skin_image_url'];
            return $this->success($reportDetail);
        }
        else{
            return $this->error(0, "fail", "网络异常，请稍后再试");
        }
    }


    public function hisotryReportList(Request $request)
    {
        $openid = $request->get("openid");
        $skinReportData = new SkinReportData();
        $report_data = $skinReportData->reportList($openid);
        $report_list = [];
        if(sizeof($report_data) > 0 ){
            foreach ($report_data as $key => $report_detail){
                $report_list[$key]['date'] = date("Y年m月d日",strtotime($report_detail['updated_at']));
                $report_list[$key]['report_id'] = $report_detail['id'];
            }
            return $this->success($report_list);
        }
        else{
            return $this->error(0, "fail", "您尚未进行过美丽诊断");
        }
    }

    public function productsRecommended(Request $request)
    {
        $openid = $request->get("openid");
        $report_id = $request->get("report_id");
        $answer['q1'] = $request->get("q1");
        $answer['q2'] = $request->get("q2");
        $answer['q3'] = $request->get("q3");
        $answer['q4'] = $request->get("q4");
        $skinReportData = new SkinReportData();
        $skinQuestion = new SkinQuestion();
        $skinRecommend = new SkinRecommend();
        $reportDetail = [];
        $result = $skinReportData->reportDetail($openid ,$report_id);
        if(sizeof($result) > 0){
            $resultDetail = $result[0];
            $reportDetail['report_id'] = $resultDetail['id'];
            $fiveSkinData['fiveAgeData'] = $resultDetail['fiveAgeData'];
            $fiveSkinData['fiveGloss'] = $resultDetail['fiveGloss'];
            $fiveSkinData['fiveStains'] = $resultDetail['fiveStains'];
            $fiveSkinData['fiveDarkCirclesData'] = $resultDetail['fiveDarkCirclesData'];
            $fiveSkinData['fiveWrinkleData'] = $resultDetail['fiveWrinkleData'];
            $reportDetail['five_images'] = $fiveSkinData;
            $reportDetail['problem'] = $skinQuestion->fiveProblemReport($fiveSkinData);
            $skinProduct = $skinRecommend->getSkinProduct($fiveSkinData);
            $q4Product = $skinRecommend->getQ4Product($answer);
            $reportDetail['problemFields'] = $skinRecommend->problemFields($fiveSkinData);
            $productIdList = array_unique(array_merge($skinProduct,$q4Product));
            $reportDetail['product'] = $skinRecommend->getRecProduct($productIdList);
            $reportDetail['skin_image_url'] = $resultDetail['skin_image_url'];
            $reportDetail['answer'] = $skinRecommend->getAnswer($answer);
            $reportDetail['skincare_ritual'] = $skinRecommend->skincareRitual($answer);
            $result = $skinReportData->answerToDB($openid, $resultDetail['id'], $answer);
            return $this->success($reportDetail);
        }
        else{
            return $this->error(0, "fail", "网络异常，请稍后再试");
        }

    }
}