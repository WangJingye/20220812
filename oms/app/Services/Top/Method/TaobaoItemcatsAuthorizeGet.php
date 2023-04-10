<?php namespace App\Services\Top\Method;

use App\Services\Top\TopAbstract;

class TaobaoItemcatsAuthorizeGet extends TopAbstract
{
    public function execute():array
    {
        return [
            'itemcats_authorize_get_response'=>[
                'seller_authorize'=>[
                    'xinpin_item_cats'=>[
                        'item_cat'=>[
                            [
                                'cid'=>'50011999',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
