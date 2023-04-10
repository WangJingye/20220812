<?php namespace App\Services\UPay\PaymentMethod;

/**
 * 公众号支付
 * Class WapPay
 * @package App\Services\Upay\PaymentMethod
 */
class WapPay extends PaymentMethod
{
    public $uPayMethod = 'wap_api_pro';

    public $magentoMethod = 'upayWx';

}
