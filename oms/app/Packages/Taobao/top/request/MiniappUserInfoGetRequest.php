<?php
/**
 * TOP API: taobao.miniapp.userInfo.get request
 * 
 * @author auto create
 * @since 1.0, 2019.01.03
 */
class MiniappUserInfoGetRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "taobao.miniapp.userInfo.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
