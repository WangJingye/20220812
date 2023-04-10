<?php namespace App\Services\UPay\PaymentMethod;

/**
 * H5微信支付
 * Class HWxPay
 * @package App\Services\Upay\PaymentMethod
 */
class HWxPay extends PaymentMethod
{
    public $uPayMethod = 'precreate';

    public $magentoMethod = 'upayWx';

    public $payWay = '3';

    public $subPayWay = '6';

}
