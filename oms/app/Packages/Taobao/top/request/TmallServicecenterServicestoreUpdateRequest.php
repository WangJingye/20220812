<?php
/**
 * TOP API: tmall.servicecenter.servicestore.update request
 * 
 * @author auto create
 * @since 1.0, 2019.03.27
 */
class TmallServicecenterServicestoreUpdateRequest
{
	/** 
	 * 网点/门店
	 **/
	private $paramServiceStoreDTO;
	
	private $apiParas = array();
	
	public function setParamServiceStoreDTO($paramServiceStoreDTO)
	{
		$this->paramServiceStoreDTO = $paramServiceStoreDTO;
		$this->apiParas["param_service_store_d_t_o"] = $paramServiceStoreDTO;
	}

	public function getParamServiceStoreDTO()
	{
		return $this->paramServiceStoreDTO;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.servicestore.update";
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
