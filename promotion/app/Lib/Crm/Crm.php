<?php

namespace App\Lib\Crm;

class Crm
{
    public function like($mix_nick){
        $crm_id = 'M2019030522460';
        return $this->behaviorPoints($crm_id,'D','F1','点赞');
    }

    public function comment($mix_nick){
        $crm_id = 'M2019030522460';
        return $this->behaviorPoints($crm_id,'D','F2','评论');
    }

    public function share($mix_nick){
        $crm_id = 'M2019030522460';
        return $this->behaviorPoints($crm_id,'D','F3','分享');
    }

    protected function behaviorPoints($crm_id,$cat,$type,$memo=''){
        if(empty($crm_id) || empty($cat) || empty($type)){
            return false;
        }
        $params = [
            'memberid'=>$crm_id,
            'txn_num'=>$cat.'_'.uniqid(),
            'date'=>date('Y-m-d'),
            'behavior_cat'=>$cat,
            'behavior_type'=>$type,
            'memo'=>$memo,
            'createdate'=>date('Y-m-d H:i:s'),
        ];
        return app()->make(\App\Lib\Crm\Mq::class)->crm('Behavior_Points',$params);
    }









}
