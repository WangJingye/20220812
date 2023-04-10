<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CrmTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
//        $resp = (new \App\Service\DLCCrm\Request)->isMember([
//            'channelID'=>'oQQmP4uvPtErIc17UPAWFiUrTZaM7',
//            'dataType'=>'mobile',
//            'data'=>'17701761517',
//        ]);
//        $resp = (new \App\Service\DLCCrm\Request)->createMember([
//            'channelID'=>'oQQmP4uvPtErIc17UPAWFiUrTZaM7',
//            'name'=>'史蒂汶7',
//            'mobile'=>'17701761517',
//            'memberCode'=>'MEC0000007',
//            'birthYear'=>0,
//            'birthMonth'=>0,
//            'birthDay'=>0,
//            'title'=>'MR'
//        ]);
//        $resp = (new \App\Service\DLCCrm\Request)->getMember([
//            'memberCode'=>'MEC0000004',
//        ]);
//        $resp = (new \App\Service\DLCCrm\Request)->bindMember([
//            'channelID'=>'oQQmP4uvPtErIc17UPAWFiUrTZaM',
//            'dataType'=>'mobile',
//            'data'=>'17701761565',
//        ]);
//        $resp = (new \App\Service\DLCCrm\Request)->updateMember([
//            'memberCode'=>'2998000070',
//            'name'=>'史蒂夫',
//            'title'=>'MS',
//        ]);
        $resp = (new \App\Service\DLCCrm\Request)->cancelMember([
            'memberCode'=>'123321',
        ]);
        var_dump($resp);exit;
//        $resp = (new \App\Service\YKSms\Request)->getRemain();
//        print_r($resp);exit;
        $this->assertTrue(true);
    }
}
