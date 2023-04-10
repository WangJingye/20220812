<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/8/10
 * Time: 11:20
 */

namespace App\Model\Skin;


class SkinFiveData
{

    public function makeSkinFiveDataDetail($meituFiveSkinData)
    {
        $skinFiveData['fiveAgeData'] = $this->makeAgeData($meituFiveSkinData['meituAge']);
        $skinFiveData['fiveGloss']  = $this->makeGlossData($meituFiveSkinData['meituGloss']);
        $skinFiveData['fiveStains']  = $this->makeStainsData($meituFiveSkinData['meituStains']);
        $skinFiveData['fiveDarkCirclesData'] = $this->makeDarkCirclesData($meituFiveSkinData['meituDarkCircles']);
        $skinFiveData['fiveWrinkleData'] = $this->makeWrinkleData($meituFiveSkinData['meituWrinkle']);
        return $skinFiveData;
    }

    /**
     * 处理年龄等级
     * @param $meituAge
     * @return int
     */
    private function makeAgeData($meituAge)
    {
        //默认给个1
        $skinAage = 1;

        if($meituAge <=20){
           // $skinAage = number_format(($meituAge / 20),1);
            $skinAage = 0;
        }
        if( $meituAge >20 && $meituAge <=30){
            //$skinAage = number_format( (( ($meituAge  - 20)/ 10) +1 ),1);
            $skinAage = 1;
        }
        if( $meituAge >30 && $meituAge <=40){
            //$skinAage = number_format( (( ($meituAge  - 30)/ 10) +2 ),1);
            $skinAage = 2;
        }
        if( $meituAge >40 && $meituAge <=50){
            //$skinAage = number_format( (( ($meituAge  - 40)/ 10) +3 ),1);
            $skinAage = 3;
        }
        if($meituAge >50){
            $skinAage = 4;
        }

        return $skinAage;
    }


    /**
     * 根据美图的SkinColorLevel生成光泽度等级
     * @param $meituGloss
     * @return mixed
     */
    private function makeGlossData($meituGloss)
    {
        //给个默认值1
        $gloss_mapping = [
            1 => 0,
            2 => 1,
            3 => 2,
            4 => 3,
            5 => 3,
            6 => 4
        ];

        $gloss_data = $gloss_mapping[$meituGloss];
        return $gloss_data;

    }

    /**
     * 根据色斑数量生成色斑情况
     * @param $meituStains
     * @return int
     */
    private function makeStainsData($meituStains)
    {
        $stain = 0;
        $count = $meituStains['value'];
        if ($count == 0){
            $stain = 0;
        }
        if($count> 0 && $count <5){
            $stain = 1;
        }
        if($count >= 5 && $count < 20){
            $stain = 2;
        }
        if($count >= 20 && $count < 50){
            $stain = 3;
        }
        if($count >=50){
            $stain = 4;
        }
        return $stain;
    }


    /**
     * 处理黑眼圈相关信息
     * @param $meituDarkCircles
     * @return int
     */
    private function makeDarkCirclesData($meituDarkCircles)
    {
        //黑眼圈默认值给0
        $DarkCirclesData = 0;

        //左眼黑眼圈0是无，1是有
        $SkinPandaEye_Left = $meituDarkCircles['SkinPandaEye_Left'];
        //右眼黑眼圈0是无，1是有
        $SkinPandaEye_Right = $meituDarkCircles['SkinPandaEye_Right'];

        //左眼色素型
        $SkinPandaEye_Left_Pigment = $meituDarkCircles['SkinPandaEye_Left_Pigment'];
        //右眼色素型
        $SkinPandaEye_Right_Pigment = $meituDarkCircles['SkinPandaEye_Right_Pigment'];


        //左眼血管型
        $SkinPandaEye_Left_Artery = $meituDarkCircles['SkinPandaEye_Left_Artery'];
        //右眼血管型
        $SkinPandaEye_Right_Artery = $meituDarkCircles['SkinPandaEye_Right_Artery'];

        //左眼阴影型程度
        $SkinPandaEye_Left_Shadow = $meituDarkCircles['SkinPandaEye_Left_Shadow'];
        //右眼阴影型程度
        $SkinPandaEye_Right_Shadow = $meituDarkCircles['SkinPandaEye_Right_Shadow'];

        //如果两只眼睛都没有任何黑眼圈症状，则为0级

        $eye_left_array = [
            $SkinPandaEye_Left,
            $SkinPandaEye_Left_Pigment,
            $SkinPandaEye_Left_Artery,
            $SkinPandaEye_Left_Shadow
        ];

        $eye_right_array = [
            $SkinPandaEye_Right,
            $SkinPandaEye_Right_Pigment,
            $SkinPandaEye_Right_Artery,
            $SkinPandaEye_Right_Shadow
        ];

        asort($eye_left_array);
        asort($eye_right_array);
        list($eye_left_key,$eye_left_value) =
            array(array_keys($eye_left_array,end($eye_left_array))[0], end($eye_left_array));
        list($eye_right_key,$eye_right_value) =
            array(array_keys($eye_right_array,end($eye_right_array))[0], end($eye_right_array));

        //两只眼睛都没有症状，则为0
        if( ($eye_left_value + $eye_right_value ) == 0){
            $DarkCirclesData = 1;
        }

        //至少一只眼睛有症状，先设定为1
        if( ($eye_left_value + $eye_right_value ) > 0){
            $DarkCirclesData = 1;
        }

        //两只眼睛都为轻度，则为2
        elseif ($eye_left_value == 1 && $eye_right_value ==1){
            $DarkCirclesData = 2;
        }

        //两只眼睛都为中度，则为3
        elseif ($eye_left_value == 2 && $eye_right_value ==2){
            $DarkCirclesData = 3;
        }

        //两只眼睛都为重度，则为4
        elseif( $eye_left_value == 3 && $eye_right_value ==3){
            $DarkCirclesData = 4;
        }

        return $DarkCirclesData;
        
    }

    //皱纹
    private function makeWrinkleData($meituWrinkle)
    {
        $wrinkleData = 0;
        //抬头纹，0或1
        $SkinForeHeadWrinkle = $meituWrinkle['SkinForeHeadWrinkle'];
        //法令纹，默认为0，如果左右侧任一处有法令纹，则为1
        $SkinNasolabial = 0;
        //左侧法令纹
        $SkinNasolabialFolds_Left = $meituWrinkle['SkinNasolabialFolds_Left'];
        //右侧法令纹
        $SkinNasolabialFolds_Right = $meituWrinkle['SkinNasolabialFolds_Right'];
        if($SkinNasolabialFolds_Left || $SkinNasolabialFolds_Right){
            $SkinNasolabial = 1;
        }

        //眼部细纹
        $SkinEyeFineLine = 0;
        $SkinEyeFineLine_Left = $meituWrinkle['SkinEyeFineLine_Left'];
        $SkinEyeFineLine_Right = $meituWrinkle['SkinEyeFineLine_Right'];

        if($SkinEyeFineLine_Left || $SkinEyeFineLine_Right){
            $SkinEyeFineLine = 1;
        }

        //鱼尾纹
        $SkinCrowsFeed = 0;
        $SkinCrowsFeed_Left = $meituWrinkle['SkinCrowsFeed_Left'];
        $SkinCrowsFeed_Right = $meituWrinkle['SkinCrowsFeed_Right'];

        if($SkinCrowsFeed_Left || $SkinCrowsFeed_Right){
            $SkinCrowsFeed = 1;
        }

        //如果4种皱纹都没有，那就是0级
        if(!$SkinForeHeadWrinkle &&  !$SkinNasolabial && !$SkinEyeFineLine && !$SkinCrowsFeed){
            $wrinkleData = 0;
        }

        //有抬头纹/法令纹/细纹其中之一
        elseif (($SkinForeHeadWrinkle + $SkinNasolabial + $SkinEyeFineLine) === 1){
            $wrinkleData = 1;
        }

        //有法令纹+眼部细纹
        elseif(( $SkinNasolabial + $SkinEyeFineLine ) === 2){
            $wrinkleData = 2;
        }

        //有抬头纹+法令纹+眼部细纹
        elseif(($SkinForeHeadWrinkle + $SkinNasolabial + $SkinEyeFineLine ) === 3){
            $wrinkleData = 3;
        }

        //有抬头纹+鱼尾纹+法令纹+眼部细纹
        elseif(($SkinForeHeadWrinkle + $SkinNasolabial + $SkinEyeFineLine + $SkinCrowsFeed) === 4){
            $wrinkleData = 4;
        }
        else{
            $wrinkleData = $SkinForeHeadWrinkle + $SkinNasolabial + $SkinEyeFineLine + $SkinCrowsFeed;
        }
        return $wrinkleData;
    }
}