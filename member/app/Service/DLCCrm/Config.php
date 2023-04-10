<?php namespace App\Service\DLCCrm;

class Config
{
    const API_MAP = [
        //判断用户
        'isMember'=>'IsMember',
        //注册用户(会员Code由商城传入)
        'createMember'=>'CreateDigitalMember',
        //绑定用户查询
        'bindMemberQuery'=>'CreateMemberBinding',
        //查询用户
        'getMember'=>'GetMemberProfile',
        //更新用户
        'updateMember'=>'UpdateMember',
        //解绑用户
        'cancelMember'=>'CancelMemberBinding',
    ];




}