<?php
/**
 * TOP API: taobao.crm.point.available.get request
 * 
 * @author auto create
 * @since 1.0, 2019.03.08
 */
class CrmPointAvailableGetRequest
{
	/** 
	 * 明文nick，可不填，直接填混淆昵称
	 **/
	private $buyerNick;
	
	/** 
	 * 混淆昵称
	 **/
	private $mixNick;
	
	private $apiParas = array();
	
	public function setBuyerNick($buyerNick)
	{
		$this->buyerNick = $buyerNick;
		$this->apiParas["buyer_nick"] = $buyerNick;
	}

	public function getBuyerNick()
	{
		return $this->buyerNick;
	}

	public function setMixNick($mixNick)
	{
		$this->mixNick = $mixNick;
		$this->apiParas["mix_nick"] = $mixNick;
	}

	public function getMixNick()
	{
		return $this->mixNick;
	}

	public function getApiMethodName()
	{
		return "taobao.crm.point.available.get";
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
