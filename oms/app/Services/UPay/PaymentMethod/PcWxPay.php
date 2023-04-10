<?php namespace App\Services\UPay\PaymentMethod;

/**
 * 二维码微信扫码支付
 * Class PcWxPay
 * @package App\Services\Upay\PaymentMethod
 */
class PcWxPay extends PaymentMethod
{
    public $uPayMethod = 'precreate';

    public $magentoMethod = 'upayWx';

    public $payWay = '3';

    public $subPayWay = '2';

}
