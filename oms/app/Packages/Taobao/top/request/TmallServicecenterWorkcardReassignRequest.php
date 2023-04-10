<?php
/**
 * TOP API: tmall.servicecenter.workcard.reassign request
 * 
 * @author auto create
 * @since 1.0, 2019.03.27
 */
class TmallServicecenterWorkcardReassignRequest
{
	/** 
	 * 请求入参
	 **/
	private $reassignStoreRequest;
	
	private $apiParas = array();
	
	public function setReassignStoreRequest($reassignStoreRequest)
	{
		$this->reassignStoreRequest = $reassignStoreRequest;
		$this->apiParas["reassign_store_request"] = $reassignStoreRequest;
	}

	public function getReassignStoreRequest()
	{
		return $this->reassignStoreRequest;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.workcard.reassign";
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
