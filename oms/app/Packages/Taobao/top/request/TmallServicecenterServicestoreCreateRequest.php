<?php
/**
 * TOP API: tmall.servicecenter.servicestore.create request
 * 
 * @author auto create
 * @since 1.0, 2019.03.30
 */
class TmallServicecenterServicestoreCreateRequest
{
	/** 
	 * 网点/门店
	 **/
	private $serviceStore;
	
	private $apiParas = array();
	
	public function setServiceStore($serviceStore)
	{
		$this->serviceStore = $serviceStore;
		$this->apiParas["service_store"] = $serviceStore;
	}

	public function getServiceStore()
	{
		return $this->serviceStore;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.servicestore.create";
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
