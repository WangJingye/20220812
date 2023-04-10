<?php namespace App\Http\Requests\Helps;

class Rules
{
    public static function Regex(){
        return [
            'mobile'=>'regex:/^1[2|3|4|5|6|7|8|9][0-9]{9}$/',
            'username'=>'regex:/^[\w\d\x{4e00}-\x{9fa5}]{1}[\.\@\_\-\s\w\d\x{4e00}-\x{9fa5}]{1,29}$/iu',
            'detail'=>'regex:/^[\w\d\x{4e00}-\x{9fa5}]{1}[\.\@\_\-\s\w\d\x{4e00}-\x{9fa5}]{1,49}$/iu',
            'remark'=>'regex:/^[\.\@\_\-\s\w\d\x{4e00}-\x{9fa5}]{0,200}$/iu',
            'birthday'=>'regex:/^[1|2][0-9]{3}\-[0-9]{2}\-[0-9]{2}$/',
            'password'=>'regex:/^(?![a-zA-Z]+$)(?![0-9]+$)[a-zA-Z0-9]{8,}$/',
            'postcode'=>'regex:/^\d*$/',
            'invoice.name'=>'regex:/^[\w\d\x{4e00}-\x{9fa5}]{1}[\.\@\_\-\s\w\d\x{4e00}-\x{9fa5}]{1,49}$/iu',
            'invoice.code'=>'regex:/^[a-zA-Z0-9]{1,49}$/iu',
        ];
    }

    public static function Attributes(){
        return [
            'username'=>'用户名',
            'mobile'=>'手机',
            'detail'=>'地址详情',
            'birthday'=>'生日',
            'password'=>'密码',
        ];
    }

}
