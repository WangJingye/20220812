<?php namespace App\Service\DLCCrm;

class Response
{
    /**
     * @param $result
     * @return bool
    "N" - phone doesn’t exist so it is a new member
    "M" – multiple active member found
    "Y" - phone exists and it is not a suspended member
    "S" - phone exists but it is a suspended member
     */
    public function isMember($result){
        if(in_array($result->Result,['N','S'])){
            //需要注册
            return true;
        }elseif(in_array($result->Result,['Y','M'])){
            //需要绑定
            return $result->MemberCode;
        }else{
            //其他情况
            return false;
        }
    }

    /**
     * @param $result
     * @return bool
    "N" – phone doesn’t exist so it is a new member
    "M" – multiple active member found
    "Y" – phone exists and it is not a suspended member
    "S" – phone exists but it is a suspended member
    "D" – Another Member Binding already exists checking by Channel + Member code or Channel + socialID
    "E" – Member Binding Exists checking by Channel + ChannelID + MemberCode or Channel + ChannelID + No MemberCode that can be resolved by Mobile

    Membercode:
    Return CRM member code if checking result is “Y”,”D”,”E”
    Return CRM member code if checking result is “M”, the member code searching priority is Purchase Date Desc, Join Date Asc, Member Code Desc
     */
    public function bindMemberQuery($result){
        $result = (array)$result;
        if(in_array($result['Result'],['N','S'])){
            //需要注册
            return true;
        }elseif(in_array($result['Result'],['Y','D','E'])){
            //已存在的用户
            return $result['MemberCode'];
        }elseif(in_array($result['Result'],['M'])){
            //已存在多个用户
            return $result['MemberCode'];
        }else{
            //其他情况
            return false;
        }
    }

    /**
     * @param $result
     * @return bool
    000=>'Member create is successful',
    100=>'Member Code is empty or already exist',
    101=>'Channel is empty',
    102=>'Channel ID (Open ID) is empty',
    103=>'Member name is not provided',
    104=>'Member mobile is not provided',
    105=>'Invalid date of birth(birthday must be a valid date and can’t be greater than today)',
    106=>'Invalid Introduce by member code',
    201=>'Mobile duplicated (for CN)',
    202=>'Member Binding (Channel + ChannelID or Channel + socialID) Exists',
    203=>'First name Kana + Home Telephone Number / Mobile duplicated, need to remove “-“ before matching (JP)
    Full name + Mobile duplicated (TW)
    Surname + Mobile duplicated (FR)',
    204=>'First name Kana + E-mail address / Mobile Email duplicate (JP except JPGUE)
    Surname + E-mail address duplicate (FR)',
     */
    public function createMember($result){
        $result = (array)$result;
        if($result['StatusCode']==='000'){
            return true;
        }return false;
    }

    public function updateMember($result){
        $result = (array)$result;
        if($result['StatusCode']==='000'){
            return true;
        }return false;
    }

    public function getMember($result){
        $result = (array)$result;
        if(empty($result['Error'])){
            foreach($result as $k=>$v){
                if(empty($v) && $v!==0){
                    $result[$k] = '';
                }
            }
            $result['MemberGender'] = array_get(['MR'=>1,'MS'=>2],$result['Reference8'],0);

            $y= $result['Reference9'];
            $m = sprintf('%02d', $result['Reference10']);
            $d = sprintf('%02d', $result['Reference11']);
            $result['MemberBirthday'] = $result['Reference9']?("{$y}-{$m}-{$d}"):'';
            $result['MemberEmail'] = $result['Reference12'];
            $result['MemberInfo'] = $result['Reference20']?json_decode($result['Reference20'],true):[];
            return $result;
        }return false;
    }

    public function cancelMember($result){
        $result = (array)$result;
        if(array_get($result,'0')=='000'){
            return true;
        }return false;
    }
}