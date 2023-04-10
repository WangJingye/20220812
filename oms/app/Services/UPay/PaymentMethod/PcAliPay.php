<?php namespace App\Services\UPay\PaymentMethod;

/**
 * 二维码支付宝扫码支付
 * Class PcAliPay
 * @package App\Services\Upay\PaymentMethod
 */
class PcAliPay extends PaymentMethod
{
    public $uPayMethod = 'precreate';

    public $magentoMethod = 'upayAli';

    public $payWay = '1';

    public $subPayWay = '2';

}
