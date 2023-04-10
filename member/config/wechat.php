<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mini_program' => [
        'app_id' => env('MINIAPPID', 'wx1f0a3f0eab450be7'),
        'secret' => env('MINIKEY', ''),
        'redirect' => 'http://localhost/socialite/callback.php',
    ],

];
