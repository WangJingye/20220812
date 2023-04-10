<?php
namespace App\Service\Condition;
use App\Service\Condition\Condition;

class FreeTry extends Condition
{
    //暂时不检测试用装的条件,08/13
    public function conditionCheck($rule,$item){
        return true;
    }
}

