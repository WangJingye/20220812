<?php
/**
 * TOP API: tmall.servicecenter.workcard.reserve request
 * 
 * @author auto create
 * @since 1.0, 2019.09.25
 */
class TmallServicecenterWorkcardReserveRequest
{
	/** 
	 * 扩展信息
	 **/
	private $attributes;
	
	/** 
	 * 预约备注信息
	 **/
	private $reserveRemark;
	
	/** 
	 * 服务结束时间
	 **/
	private $reserveTimeEnd;
	
	/** 
	 * 服务开始时间
	 **/
	private $reserveTimeStart;
	
	/** 
	 * 工单id
	 **/
	private $workcardId;
	
	/** 
	 * 工人手机号
	 **/
	private $workerMobile;
	
	/** 
	 * 工人姓名
	 **/
	private $workerName;
	
	private $apiParas = array();
	
	public function setAttributes($attributes)
	{
		$this->attributes = $attributes;
		$this->apiParas["attributes"] = $attributes;
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function setReserveRemark($reserveRemark)
	{
		$this->reserveRemark = $reserveRemark;
		$this->apiParas["reserve_remark"] = $reserveRemark;
	}

	public function getReserveRemark()
	{
		return $this->reserveRemark;
	}

	public function setReserveTimeEnd($reserveTimeEnd)
	{
		$this->reserveTimeEnd = $reserveTimeEnd;
		$this->apiParas["reserve_time_end"] = $reserveTimeEnd;
	}

	public function getReserveTimeEnd()
	{
		return $this->reserveTimeEnd;
	}

	public function setReserveTimeStart($reserveTimeStart)
	{
		$this->reserveTimeStart = $reserveTimeStart;
		$this->apiParas["reserve_time_start"] = $reserveTimeStart;
	}

	public function getReserveTimeStart()
	{
		return $this->reserveTimeStart;
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

	public function setWorkerMobile($workerMobile)
	{
		$this->workerMobile = $workerMobile;
		$this->apiParas["worker_mobile"] = $workerMobile;
	}

	public function getWorkerMobile()
	{
		return $this->workerMobile;
	}

	public function setWorkerName($workerName)
	{
		$this->workerName = $workerName;
		$this->apiParas["worker_name"] = $workerName;
	}

	public function getWorkerName()
	{
		return $this->workerName;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.workcard.reserve";
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
