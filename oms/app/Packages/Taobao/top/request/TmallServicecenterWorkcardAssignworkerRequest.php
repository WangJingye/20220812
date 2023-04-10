<?php
/**
 * TOP API: tmall.servicecenter.workcard.assignworker request
 * 
 * @author auto create
 * @since 1.0, 2019.10.14
 */
class TmallServicecenterWorkcardAssignworkerRequest
{
	/** 
	 * 需要派工人的外部核销单id
	 **/
	private $outId;
	
	/** 
	 * 需要指派的工人id
	 **/
	private $targetWorkerId;
	
	/** 
	 * 需要指派的工人手机
	 **/
	private $targetWorkerMobile;
	
	/** 
	 * 需要指派的工人姓名
	 **/
	private $targetWorkerName;
	
	/** 
	 * 需要派工人的工单id
	 **/
	private $workcardId;
	
	private $apiParas = array();
	
	public function setOutId($outId)
	{
		$this->outId = $outId;
		$this->apiParas["out_id"] = $outId;
	}

	public function getOutId()
	{
		return $this->outId;
	}

	public function setTargetWorkerId($targetWorkerId)
	{
		$this->targetWorkerId = $targetWorkerId;
		$this->apiParas["target_worker_id"] = $targetWorkerId;
	}

	public function getTargetWorkerId()
	{
		return $this->targetWorkerId;
	}

	public function setTargetWorkerMobile($targetWorkerMobile)
	{
		$this->targetWorkerMobile = $targetWorkerMobile;
		$this->apiParas["target_worker_mobile"] = $targetWorkerMobile;
	}

	public function getTargetWorkerMobile()
	{
		return $this->targetWorkerMobile;
	}

	public function setTargetWorkerName($targetWorkerName)
	{
		$this->targetWorkerName = $targetWorkerName;
		$this->apiParas["target_worker_name"] = $targetWorkerName;
	}

	public function getTargetWorkerName()
	{
		return $this->targetWorkerName;
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
		return "tmall.servicecenter.workcard.assignworker";
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
