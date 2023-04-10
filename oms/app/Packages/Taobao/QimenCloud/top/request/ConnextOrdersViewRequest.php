<?php
/**
 * TOP API: 154o3iz55l.connext.orders.view request
 * 
 * @author auto create
 * @since 1.0, 2018.12.11
 */
class ConnextOrdersViewRequest
{
	/** 
	 * endTime
	 **/
	private $endTime;
	
	/** 
	 * shop
	 **/
	private $shop;
	
	/** 
	 * startTime
	 **/
	private $startTime;
	
	private $apiParas = array();
	
	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
		$this->apiParas["endTime"] = $endTime;
	}

	public function getEndTime()
	{
		return $this->endTime;
	}

	public function setShop($shop)
	{
		$this->shop = $shop;
		$this->apiParas["shop"] = $shop;
	}

	public function getShop()
	{
		return $this->shop;
	}

	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
		$this->apiParas["startTime"] = $startTime;
	}

	public function getStartTime()
	{
		return $this->startTime;
	}

	public function getApiMethodName()
	{
		return "154o3iz55l.connext.orders.view";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->endTime,"endTime");
		RequestCheckUtil::checkNotNull($this->shop,"shop");
		RequestCheckUtil::checkNotNull($this->startTime,"startTime");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
