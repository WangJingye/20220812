<?php
/*
|--------------------------------------------------------------------------
| web my ajax Routes
|--------------------------------------------------------------------------
*/
Route::post('article/status', 'PageController@status');
Route::post('article/del', 'PageController@del');
Route::post('file/uploadImage','FileController@uploadImage');
Route::post('taxonomy/list','TaxonomyController@dataList');
