<?php
/**
 * TOP API: alibaba.service.billing.query request
 * 
 * @author auto create
 * @since 1.0, 2019.04.03
 */
class AlibabaServiceBillingQueryRequest
{
	/** 
	 * 账单查询结束时间，时间区间限制未15分钟。 格式示例 2019-03-26 17:15:28
	 **/
	private $gmtCreateEnd;
	
	/** 
	 * 账单查询开始时间。格式示例 2019-03-26 17:15:28
	 **/
	private $gmtCreateStart;
	
	private $apiParas = array();
	
	public function setGmtCreateEnd($gmtCreateEnd)
	{
		$this->gmtCreateEnd = $gmtCreateEnd;
		$this->apiParas["gmt_create_end"] = $gmtCreateEnd;
	}

	public function getGmtCreateEnd()
	{
		return $this->gmtCreateEnd;
	}

	public function setGmtCreateStart($gmtCreateStart)
	{
		$this->gmtCreateStart = $gmtCreateStart;
		$this->apiParas["gmt_create_start"] = $gmtCreateStart;
	}

	public function getGmtCreateStart()
	{
		return $this->gmtCreateStart;
	}

	public function getApiMethodName()
	{
		return "alibaba.service.billing.query";
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
