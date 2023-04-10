<?php
return [
    /*后台上传相关配置*/
    'upload'=>'/upload',
    'upload_image_type'=>['jpg','jpeg','png','gif'],
    'upload_image_size'=>'51457280',

    /*其他*/
    'author_type'=>[1=>'素人',2=>'达人'],
    'items_type'=>[1=>'小样',2=>'正装',3=>'积分加钱购'],
    'comments_status'=>[0=>'待审核',1=>'审核通过',2=>'审核未通过'],
    'integral_type'=>[1=>'点赞',2=>'评论',3=>'转发'],
    'points_type'=>[1=>1,2=>2,3=>3],//类型=>分值(对应 comments_status)
    'max_day_points'=>20,//当日所能获得的最大积分数
    'export_max_length'=>99999,//后台导出最大数量

];