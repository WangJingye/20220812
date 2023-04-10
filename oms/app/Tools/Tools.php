<?php
/**
 *  ===========================================
 *  File Name   Http.php
 *  Class Name  Http
 *  Date        2020-07-28 14:13
 *  Created by  William Ji
 *  ===========================================
 **/

namespace App\Tools;

class Tools
{
    /**
     * 格式化二维数组中列的数据
     */
    public static function formatNumberInArray(array $inputArray, array $columns)
    {
        if (!empty($inputArray)) {
            foreach ($columns as $column) {
                foreach ($inputArray as $rKey => $row) {
                    $inputArray[$rKey][$column] = number_format($row[$column]);
                }
            }
        }

        return $inputArray;
    }
}
