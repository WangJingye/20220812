<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/8/1
 * Time: 11:50
 */

namespace App\Model\Skin;

use App\Model\Skin\SkinOssClient as SkinOssClient;
use App\Model\Skin\SkinFiveData as SkinFiveData;
use App\Model\Skin\SkinReportData as SkinReportData;
use App\Model\Skin\SkinQuestion as SkinQuestion;

class SkinAnalysis
{

    public function skinTest($open_id, $skin_image_url){
        //请求先进入美图皮肤分析接口
        $meitu_skin_response = $this->meituSkin($skin_image_url);
        $skinOssClient = new SkinOssClient();
        $object = $open_id;
        //将测肤结果存一份到OSS中,下次再再用到可以直接去里面取
        $oss_original_name = $skinOssClient->meituOriginalDataToOss($object, $meitu_skin_response);
        //校验美图返回值结果是否OK
        $face_attributes = $this->checkMeituData($meitu_skin_response);
        //返回值不符合要求的话，就直接返回报错
        if(!is_array($face_attributes)){
            return $face_attributes;
        }
        //符合要求的，先存到数据库里面？
        $fiveSkinData = $this->getFiveSkinData($face_attributes);
        $skinReportData = new SkinReportData();
        $skinQuestion = new SkinQuestion();
        $result = $skinReportData->reportToDB($open_id, $oss_original_name, $fiveSkinData ,$skin_image_url);
        $skinTestReport['five_images'] = $fiveSkinData;
        $skinTestReport['problem'] = $skinQuestion->fiveProblemReport($fiveSkinData);
        $skinTestReport['skin_image_url'] = $skin_image_url;
        $skinTestReport['report_id'] = $result['id'];
        return $skinTestReport;

    }


    /**
     * 按照美图肌肤测试封装测试
     * @param $skin_image_url
     * @return mixed
     */
    public function meituSkin($skin_image_url){
        $skinData = json_encode([
            'parameter' => [
                'nFront' => 1,
            ],
            'extra' => (object)[],
            'media_info_list' => [
                [
                    'media_data' => $skin_image_url,
                    'media_profiles' => [
                        'media_data_type' => 'url'
                    ],
                ]
            ]
        ]);

        $app_id = config("meitu.api_key");
        $api_secret = config("meitu.api_secret");


        $aiBeautyUrl = "https://openapi.mtlab.meitu.com/v2/skin?api_key=$app_id&api_secret=$api_secret";

        $response = http_curl($aiBeautyUrl, $skinData);
        return $response;
    }

    public function getFiveSkinData($face_attributes){
        $skinFiveData = new SkinFiveData();
        $meituFiveSkinData = [];
        $meituFiveSkinData['meituAge'] = $face_attributes['SkinAge']['value'];
        //第一个维度是肌肤年龄，范围为[0,100)间的整数。

        $meituFiveSkinData['meituGloss'] = $face_attributes['SkinColorLevel']['value'];
        //光泽度,返回值是1-6，6个档次。肤色明暗程度

        //色斑这个维度，还是比较头疼。美图给有数量和范围，这里用数量
        $meituFiveSkinData['meituStains'] = $face_attributes['SkinSpot'];

        //黑眼圈这个维度，我这边就是用几个参数识别出来时都都有
        $meituFiveSkinData['meituDarkCircles']['SkinPandaEye_Left'] = $face_attributes['SkinPandaEye_Left']['value'];
        $meituFiveSkinData['meituDarkCircles']['SkinPandaEye_Right'] = $face_attributes['SkinPandaEye_Right']['value'];
        $meituFiveSkinData['meituDarkCircles']['SkinPandaEye_Left_Pigment'] = $face_attributes['SkinPandaEye_Left_Pigment']['value'];
        $meituFiveSkinData['meituDarkCircles']['SkinPandaEye_Right_Pigment'] = $face_attributes['SkinPandaEye_Right_Pigment']['value'];
        $meituFiveSkinData['meituDarkCircles']['SkinPandaEye_Left_Artery'] = $face_attributes['SkinPandaEye_Left_Artery']['value'];
        $meituFiveSkinData['meituDarkCircles']['SkinPandaEye_Right_Artery'] = $face_attributes['SkinPandaEye_Right_Artery']['value'];
        $meituFiveSkinData['meituDarkCircles']['SkinPandaEye_Left_Shadow'] = $face_attributes['SkinPandaEye_Left_Shadow']['value'];
        $meituFiveSkinData['meituDarkCircles']['SkinPandaEye_Right_Shadow'] = $face_attributes['SkinPandaEye_Right_Shadow']['value'];


        //皱纹这个，我需要多看几个参数。
        $meituFiveSkinData['meituWrinkle']['SkinForeHeadWrinkle'] = $face_attributes['SkinForeHeadWrinkle']['value'];
        $meituFiveSkinData['meituWrinkle']['SkinNasolabialFolds_Left'] = $face_attributes['SkinNasolabialFolds_Left']['value'];
        $meituFiveSkinData['meituWrinkle']['SkinNasolabialFolds_Right'] = $face_attributes['SkinNasolabialFolds_Right']['value'];
        $meituFiveSkinData['meituWrinkle']['SkinForeHeadWrinkle'] = $face_attributes['SkinForeHeadWrinkle']['value'];
        $meituFiveSkinData['meituWrinkle']['SkinForeHeadWrinkle'] = $face_attributes['SkinForeHeadWrinkle']['value'];


        $meituFiveSkinData['meituWrinkle']['skinForeHeadWrinkleArea'] = $face_attributes['SkinForeHeadWrinkleArea']['value'];
        $meituFiveSkinData['meituWrinkle']['SkinCrowsFeed_Left'] = $face_attributes['SkinCrowsFeed_Left']['value'];
        $meituFiveSkinData['meituWrinkle']['SkinCrowsFeed_Right'] = $face_attributes['SkinCrowsFeed_Right']['value'];
        $meituFiveSkinData['meituWrinkle']['SkinEyeFineLine_Left'] = $face_attributes['SkinEyeFineLine_Left']['value'];
        $meituFiveSkinData['meituWrinkle']['SkinEyeFineLine_Right'] = $face_attributes['SkinEyeFineLine_Right']['value'];
        $meituFiveSkinData['meituWrinkle']['CrowsFeed_LeftArea'] = $face_attributes['CrowsFeed_LeftArea']['value'];
        $meituFiveSkinData['meituWrinkle']['CrowsFeed_RightArea'] = $face_attributes['CrowsFeed_RightArea']['value'];
        $meituFiveSkinData['meituWrinkle']['EyeWrinkle_LeftArea'] = $face_attributes['EyeWrinkle_LeftArea']['value'];
        $meituFiveSkinData['meituWrinkle']['EyeWrinkle_RightArea'] = $face_attributes['EyeWrinkle_RightArea']['value'];
        $skinFiveDataDetail = $skinFiveData->makeSkinFiveDataDetail($meituFiveSkinData);
        return $skinFiveDataDetail;
    }


    public function fiveReport($fiveData)
    {


        $problem1['title'] = "肌肤暗沉无光泽";
        $problem1['description'] = "色斑明显肤色不均，紫外线照射和肌肤代谢速度慢
                                    是色斑形成重要原因，建议从防晒和美白方面同步加强。";
        $problem2['title'] = "脸部皱纹较明显";
        $problem2['description'] = "色斑明显肤色不均，紫外线照射和肌肤代谢速度慢
                                    是色斑形成重要原因，建议从防晒和美白两方面同步加强。";
        $fiveDataReport[] = $problem1;
        $fiveDataReport[] = $problem2;
        return $fiveDataReport;
    }

    public function checkMeituData($meitu_skin_response){

        $meituData_array = json_decode($meitu_skin_response, true);
        if(isset($meituData_array['media_info_list'])){
            $face_attributes = $meituData_array['media_info_list'][0]['media_extra']['faces'][0]['face_attributes'];
            return $face_attributes;
        }
        else{
            //这里最好是触发一些预警机制
            if ($meituData_array['ErrorCode'] == 20003){
                $ErrorMsg = "人脸缺失，请重新上传照片";
            }
            else{
                $ErrorMsg = "网络异常，请重试";
            }
            return $ErrorMsg;
        }
    }
}