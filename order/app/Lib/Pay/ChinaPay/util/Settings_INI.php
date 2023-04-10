<?php
namespace App\Lib\Pay\ChinaPay\util;

use App\Lib\Pay\ChinaPay\util\Settings;

//include ('Settings.php');

class Settings_INI extends Settings
{
   function load($file=NULL)
    {
        if (file_exists($file) == false) {
            return false;
        }
        $this->_settings = parse_ini_file($file, true);
    }
}
?>