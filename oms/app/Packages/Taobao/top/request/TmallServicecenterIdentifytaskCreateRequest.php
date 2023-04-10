<?php
/**
 * TOP API: tmall.servicecenter.identifytask.create request
 * 
 * @author auto create
 * @since 1.0, 2019.09.16
 */
class TmallServicecenterIdentifytaskCreateRequest
{
	/** 
	 * 服务商自定义的外部核销单id
	 **/
	private $outerId;
	
	/** 
	 * 是否改派
	 **/
	private $reassign;
	
	/** 
	 * 工单列表
	 **/
	private $workcardIds;
	
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

	public function setReassign($reassign)
	{
		$this->reassign = $reassign;
		$this->apiParas["reassign"] = $reassign;
	}

	public function getReassign()
	{
		return $this->reassign;
	}

	public function setWorkcardIds($workcardIds)
	{
		$this->workcardIds = $workcardIds;
		$this->apiParas["workcard_ids"] = $workcardIds;
	}

	public function getWorkcardIds()
	{
		return $this->workcardIds;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.identifytask.create";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->outerId,"outerId");
		RequestCheckUtil::checkNotNull($this->workcardIds,"workcardIds");
		RequestCheckUtil::checkMaxListSize($this->workcardIds,20,"workcardIds");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
