<?php
/**
 * TOP API: tmall.servicecenter.workcard.suspend request
 * 
 * @author auto create
 * @since 1.0, 2019.11.15
 */
class TmallServicecenterWorkcardSuspendRequest
{
	/** 
	 * 挂起原因类型code
	 **/
	private $failCode;
	
	/** 
	 * 挂起原因描述
	 **/
	private $failDesc;
	
	/** 
	 * 下次联系时间
	 **/
	private $gmtNextContact;
	
	/** 
	 * 预约时间
	 **/
	private $reserveServiceDate;
	
	/** 
	 * 工单id
	 **/
	private $workcardId;
	
	private $apiParas = array();
	
	public function setFailCode($failCode)
	{
		$this->failCode = $failCode;
		$this->apiParas["fail_code"] = $failCode;
	}

	public function getFailCode()
	{
		return $this->failCode;
	}

	public function setFailDesc($failDesc)
	{
		$this->failDesc = $failDesc;
		$this->apiParas["fail_desc"] = $failDesc;
	}

	public function getFailDesc()
	{
		return $this->failDesc;
	}

	public function setGmtNextContact($gmtNextContact)
	{
		$this->gmtNextContact = $gmtNextContact;
		$this->apiParas["gmt_next_contact"] = $gmtNextContact;
	}

	public function getGmtNextContact()
	{
		return $this->gmtNextContact;
	}

	public function setReserveServiceDate($reserveServiceDate)
	{
		$this->reserveServiceDate = $reserveServiceDate;
		$this->apiParas["reserve_service_date"] = $reserveServiceDate;
	}

	public function getReserveServiceDate()
	{
		return $this->reserveServiceDate;
	}

	public function setWorkcardId($workcardId)
	{
		$this->workcardId = $workcardId;
		$this->apiParas["workcard_id"] = $workcardId;
	}

	public function getWorkcardId()
	{
		return $this->workcardId;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.workcard.suspend";
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
