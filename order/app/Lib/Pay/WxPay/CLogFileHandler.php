<?php
namespace App\Lib\Pay\WxPay;
use App\Lib\Pay\WxPay\ILogHandler;


//以下为日志

class CLogFileHandler implements ILogHandler
{
    private $handle = null;
    
    public function __construct($file = '')
    {
        $this->handle = fopen($file,'a');
    }
    
    public function write($msg)
    {
        fwrite($this->handle, $msg, 4096);
    }
    
    public function __destruct()
    {
        fclose($this->handle);
    }
}
