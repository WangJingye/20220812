<?php
/** @var \Laravel\Lumen\Routing\Router $router */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

//测试接口
$router->group([
    'namespace' =>'Test',
    'prefix' => '/test',
    'middleware' => ['log','format:api']
], function ($router) {
    //恒康测试接口
    $router->post('tb/{method}', 'TopController@tb');
    //收钱吧测试接口
    $router->get('upay', 'UPayController@pay');
    $router->get('testm', 'MagentoController@test');
    $router->post('check-sign', 'UPayController@checkSign');
    $router->post('send-invoice', 'UPayController@sendInvoice');
    //微信测试接口
    $router->post('wechat/serve', 'WechatController@serve');
    $router->post('test', 'TestController@test');
    $router->post('testcart', 'TestController@testcart');
    $router->post('testsms', 'TestController@testsms');
});

//Swoole接口
$router->group([
    'namespace' => 'Swoole',
    'middleware' => ['throttle:60,1']
], function ($router) {
    $router->post('notify/send', 'NotifyController@send');
});

//商品接口
$router->group(['namespace' => 'Product',
    'prefix' => 'product',
], function() use ($router)
{
    $router->post('updateStock', 'StockController@update');
});


