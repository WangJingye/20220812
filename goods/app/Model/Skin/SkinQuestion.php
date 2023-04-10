<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/9/24
 * Time: 14:00
 */

namespace App\Model\Skin;


use function Complex\asec;

class SkinQuestion
{
    //肌龄文案

    static $fiveQuestion_list = [
        'fiveAgeData' => [
            2 => [
                'title' => '保养得当肌龄尚佳',
                'description' => '肌龄尚佳，新陈代谢及修复能力均处于理想状态，建议注重抗氧化工作，白天使用具有抗氧化功能的化妆水和乳液，日常注意防晒避免熬夜等。'
            ],
            3 => [
                'title' => '肌龄不佳有初老症状',
                'description' => '肌肤状况开始走下坡路，但仍不失抵抗力，弹性尚好。建议配合紧致抗老的护肤品，尤其眼霜要放弃清爽的眼部啫喱，改用滋润型眼霜，有效预防细纹产生。'
            ],
            4 => [
                'title' => '肌肤老化严重',
                'description' => '脸色灰黯，皱纹明显，肌肤年龄不乐观。建议强化肌肤的弹性和保湿，使用油份含量较高的保湿产品和紧致抗皱产品，同时还要注意饮食，保持宽容愉悦的心境。'
            ]
        ],
        'fiveGloss' => [
            2 => [
                'title' => '肤色自然但略失光泽',
                'description' => '肤色自然不够透亮，需注重肌肤的水润饱满。日常补水保湿的同时，也要避免熬夜注重饮食作息。'
            ],
            3 => [
                'title' => '哑光肌肤略失光泽',
                'description' => '肤色呈现哑光状态，且光泽度渐失。注重补水保湿和防晒工作的同时，也要避免熬夜注意饮食作息。'
            ],
            4 => [
                'title' => '肌肤暗沉无光泽',
                'description' => '肌肤暗沉毫无光泽度，建议加强肌肤的亮白保湿工作，避免熬夜注意饮食作息。'
            ]
        ],
        'fiveStains' => [
            2 => [
                'title' => '脸部略有色斑肤色整体均匀',
                'description' => '有些许色斑，日常需要注重保湿和一年四季的防晒工作，配合美白淡斑减少色斑。'
            ],
            3 => [
                'title' => '脸部色斑明显',
                'description' => '色斑较明显，建议从防晒和美白两步走，无时无刻的防晒阻止肌肤色斑进一步加重，配合美白减少色斑均匀肤色。'
            ],
            4 => [
                'title' => '肤色不均色斑严重',
                'description' => '色斑明显肤色不均，皮肤底层黑色素过度沉着，建议立即加强防晒和美白，避免肌肤进一步老化，同时改善体内新陈代谢修护肌肤。'
            ]
        ],
        'fiveDarkCirclesData' => [
            2 => [
                'title' => '眼部有淡淡黑眼圈',
                'description' => '建议使用眼部护理产品，淡化黑眼圈有效防止眼部细纹产生，同时注重眼部保湿和充足睡眠。'
            ],
            3 => [
                'title' => '眼部黑眼圈眼袋较明显',
                'description' => '黑眼圈略严重，建议尽快采用眼霜补救，淡化黑眼圈改善眼部细纹，强韧脆弱眼周修护防御，同时要注重眼部保湿和作息规律。'
            ],
            4 => [
                'title' => '黑眼圈眼袋严重',
                'description' => '黑眼圈严重，眼袋明显，需即刻重视眼部问题。眼部肌肤缺水、化妆品清除不彻底和生活不规律等都是导致黑眼圈的罪魁祸首。'
            ]
        ],
        'fiveWrinkleData' => [
            2 => [
                'title' => '眼部有微小细纹',
                'description' => '眼部开始出现细纹，面部整体弹性和紧致度尚可，日常注重眼部保湿和防晒工作即可。'
            ],
            3 => [
                'title' => '肌肤皱纹略明显',
                'description' => '眼部细纹和脸部法令纹等已出现，建议使用一些抗老产品帮助肌肤抵御衰老恢复弹性，此外日常的保湿防晒也是避免肌肤进一步老化的重要措施之一。'
            ],
            4 => [
                'title' => '肌肤皱纹严重',
                'description' => '皱纹严重，鱼尾纹法令纹等均比较明显，需要迅速采用紧致祛皱面霜和眼霜避免进一步衰老，同时还要注重手法和力道。此外，保湿防晒也是必不可少的工作之一。'
            ]
        ]
    ];


    static $fiveNormalData_list = [
        'title' => '肌肤状况良好，请继续保持',
        'description' => ''
    ];

    /**
     * 五维图问题点读取商品
     * @param $fiveData
     * @return array
     */
    public function fiveProblemReport($fiveData)
    {
        $problem = [];
        asort($fiveData);
        list($key,$value) =
            array(array_keys($fiveData,end($fiveData))[0], end($fiveData));
        if($value <= 1){
            $problem[]   = self::$fiveNormalData_list;
        }
        else{
            $problem[] = self::$fiveQuestion_list[$key][$value];
        }
        return $problem;
    }

}