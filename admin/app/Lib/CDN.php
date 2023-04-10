<?php

/**
 * User: JIWI001
 * Date: 2018/12/18
 * Time: 15:07.
 */

namespace App\Lib;


class CDN
{
    public $cdnHost = 'cdn.aliyuncs.com';
    public $cdnVersion = '2018-05-10';
    public $signMethod = 'HMAC-SHA1';
    public $signVersion = '1.0';

    public function getCommonArgs()
    {
        return [
            'Format' => 'JSON',
            'Version' => $this->cdnVersion,
            'AccessKeyId' => env("ACCESS_KEY_ID"),
            'SignatureMethod' => $this->signMethod,
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'SignatureVersion' => $this->signVersion,
            'SignatureNonce' => $this->uuid('CDN'),
        ];
    }

    public function refreshFile($filePath)
    {
        $commonArgs = $this->getCommonArgs();
        $commonArgs['Action'] = 'RefreshObjectCaches';
        $commonArgs['ObjectPath'] = $filePath;
        $commonArgs['ObjectType'] = 'File';
        $url = $this->getStringToSign($commonArgs);

        return Curl::curl($url);
    }

    private function getStringToSign($param)
    {
        $str = '';
        $StringToSign = $this->rpcString('GET', $param, $str);

        $signature = $this->sign($StringToSign, env("ACCESS_KEY_SECRET").'&');
        $url = 'https://'.$this->cdnHost.'?'.$str.'&Signature='.$this->percentEncode($signature);

        return $url;
    }

    /**
     * @param string $method
     * @param array  $parameters
     *
     * @return string
     */
    public function rpcString($method, array $parameters, &$str)
    {
        ksort($parameters);
        $canonicalized = '';
        $str = '';
        foreach ($parameters as $key => $value) {
            $canonicalized .= '&'.$this->percentEncode($key).'='.$this->percentEncode($value);
        }

        $str = substr($canonicalized, 1);

        return $method.'&%2F&'.$this->percentEncode(substr($canonicalized, 1));
    }

    /**
     * @param string $string
     *
     * @return null|string|string[]
     */
    public function percentEncode($string)
    {
        $result = urlencode($string);
        $result = str_replace(['+', '*'], ['%20', '%2A'], $result);
        $result = preg_replace('/%7E/', '~', $result);

        return $result;
    }

    /**
     * @param string $string
     * @param string $accessKeySecret
     *
     * @return string
     */
    public function sign($string, $accessKeySecret)
    {
        return base64_encode(hash_hmac('sha1', $string, $accessKeySecret, true));
    }

    /**
     * @param string $salt
     *
     * @return string
     */
    public function uuid($salt)
    {
        return md5($salt.uniqid(md5(microtime(true)), true));
    }
}
