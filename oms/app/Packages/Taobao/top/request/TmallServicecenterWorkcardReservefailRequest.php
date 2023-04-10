<?php
/**
 * TOP API: tmall.servicecenter.workcard.reservefail request
 * 
 * @author auto create
 * @since 1.0, 2019.10.14
 */
class TmallServicecenterWorkcardReservefailRequest
{
	/** 
	 * 预约失败原因码
	 **/
	private $failCode;
	
	/** 
	 * 预约失败原因描述
	 **/
	private $failDesc;
	
	/** 
	 * 下次联系时间
	 **/
	private $gmtNextContact;
	
	/** 
	 * 核销单外部id
	 **/
	private $identifyTaskId;
	
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

	public function setIdentifyTaskId($identifyTaskId)
	{
		$this->identifyTaskId = $identifyTaskId;
		$this->apiParas["identify_task_id"] = $identifyTaskId;
	}

	public function getIdentifyTaskId()
	{
		return $this->identifyTaskId;
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
		return "tmall.servicecenter.workcard.reservefail";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->failCode,"failCode");
		RequestCheckUtil::checkNotNull($this->failDesc,"failDesc");
		RequestCheckUtil::checkNotNull($this->gmtNextContact,"gmtNextContact");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
