<?php
namespace App\Services\Pay;

use App\Lib\Http;

class UPayService
{

	public function __construct()
	{
		$this->cartModel = new CartModel();
	}
}