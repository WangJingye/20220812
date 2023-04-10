<?php

return [


    'user' => [
        'favorite'=>config('app.name').':user:favorite:{user_id}',
        'footprint'=>config('app.name').':user:footprint:{user_id}',
        'address'=>config('app.name').':user:address:{user_id}',
        'account'=>config('app.name').':user:account:{account}',
        'phone'=>config('app.name').':user:phone:{phone}',
        'info'=>config('app.name').':user:info:{user_id}',
        'tmp_uid'=>config('app.name').':user:tmp:uid:{openid}',
        'coupon'=>config('app.name').':user:coupon:{user_id}',
    ],


];