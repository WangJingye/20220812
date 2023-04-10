<?php

return [

    'dlc_oms_app_key'=>env('OMS_APP_KEY'),
    'dlc_oms_app_secret'=>env('OMS_APP_SECRET'),
    'dlc_oms_url'=>env('OMS_URL'),
    'dlc_oms_shop_id'=>env('OMS_SHOP_ID'),
    //待支付提醒(秒)(默认2小时-15分钟=取消前15分钟)
    'order_pending_remind'=>env('ORDER_PENDING_REMIND'),



];