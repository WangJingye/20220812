<?php
/**
 * TOP API: tmall.servicecenter.workcard.verifycode.resend request
 * 
 * @author auto create
 * @since 1.0, 2019.03.27
 */
class TmallServicecenterWorkcardVerifycodeResendRequest
{
	/** 
	 * 门店/网点id
	 **/
	private $serviceStoreId;
	
	/** 
	 * 工单id
	 **/
	private $workcardId;
	
	private $apiParas = array();
	
	public function setServiceStoreId($serviceStoreId)
	{
		$this->serviceStoreId = $serviceStoreId;
		$this->apiParas["service_store_id"] = $serviceStoreId;
	}

	public function getServiceStoreId()
	{
		return $this->serviceStoreId;
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
		return "tmall.servicecenter.workcard.verifycode.resend";
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
