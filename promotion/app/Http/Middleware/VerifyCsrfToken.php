<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'admin/page/ajaxUpload',
        'admin/page/tree',
        'admin/page/files',
        'admin/page/ajaxAuthor',
        'admin/page/ajaxConfig',
        'admin/promotion/cateogry/dataList',
    ];
}
