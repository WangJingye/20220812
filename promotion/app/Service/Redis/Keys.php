<?php
namespace App\Service\Redis;

//促销redis Keys
class Keys
{
    public static function getRuleKeys(){
        return 'dlc_rules';
    }
}