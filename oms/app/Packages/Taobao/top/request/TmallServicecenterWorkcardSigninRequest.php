<?php
/**
 * TOP API: tmall.servicecenter.workcard.signin request
 * 
 * @author auto create
 * @since 1.0, 2019.10.14
 */
class TmallServicecenterWorkcardSigninRequest
{
	/** 
	 * 服务商回传的核销单外部id
	 **/
	private $outerId;
	
	/** 
	 * 服务商回传的工单id
	 **/
	private $workcardId;
	
	private $apiParas = array();
	
	public function setOuterId($outerId)
	{
		$this->outerId = $outerId;
		$this->apiParas["outer_id"] = $outerId;
	}

	public function getOuterId()
	{
		return $this->outerId;
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
		return "tmall.servicecenter.workcard.signin";
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
