<?php
/**
 * TOP API: tmall.servicecenter.settlement.storetransfer.audit request
 * 
 * @author auto create
 * @since 1.0, 2019.10.08
 */
class TmallServicecenterSettlementStoretransferAuditRequest
{
	/** 
	 * 审批通过
	 **/
	private $auditPass;
	
	/** 
	 * 工单id
	 **/
	private $workcardId;
	
	private $apiParas = array();
	
	public function setAuditPass($auditPass)
	{
		$this->auditPass = $auditPass;
		$this->apiParas["audit_pass"] = $auditPass;
	}

	public function getAuditPass()
	{
		return $this->auditPass;
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
		return "tmall.servicecenter.settlement.storetransfer.audit";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->workcardId,"workcardId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
