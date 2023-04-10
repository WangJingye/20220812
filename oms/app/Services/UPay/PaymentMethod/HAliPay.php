<?php namespace App\Services\UPay\PaymentMethod;

/**
 * H5支付宝
 * Class HAliPay
 * @package App\Services\Upay\PaymentMethod
 */
class HAliPay extends PaymentMethod
{
    public $uPayMethod = 'precreate';

    public $magentoMethod = 'upayAli';

    public $payWay = '1';

    public $subPayWay = '6';

}
