<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/9/28
 * Time: 14:06
 */

namespace App\Model\Skin;


class SkinFaqProduct
{


    //Q2没选择25，非敏感肌肤
    static $q2_none_product_list = [
        '41' => [231,232],
        '42' => [252,255],
        '43' => [250,255],
        '44' => [268,248],
        '45' => [266,238]
    ];


    //Q2选了25，极其敏感
    static $q2_yes_product_list = [
        '41' => [227],
        '42' => [252,255],
        '43' => [250,255],
        '44' => [268,248],
        '45' => [266,238]
    ];
}