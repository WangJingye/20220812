<?php
namespace App\Service\Rule\Point;

//悦享钱，可以赚取的悦享钱, 可以使用的悦享钱
//

class PointAbstract
{
    protected $can_use_points_cids = ['GF','PF','MP','DF','DI','XF','GS','QF','SS','TF','PL','FJ','SF','CHARME'];
    protected $can_ean_points_cids_fixed = ['GF','PF','MP','DF','DI','XF','GS','QF','SS','TF','PL','FJ','SF',];
    protected $can_ean_points_cids_non_fixed = ['GA','PA',];
    protected $charme_string = 'CHARME';
}

