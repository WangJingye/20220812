<?php
/**
 * TOP API: tmall.mallitemcenter.supplier.price.upload request
 * 
 * @author auto create
 * @since 1.0, 2019.08.30
 */
class TmallMallitemcenterSupplierPriceUploadRequest
{
	/** 
	 * 服务商门店价格列表
	 **/
	private $providerPriceList;
	
	/** 
	 * 服务code
	 **/
	private $serviceCode;
	
	private $apiParas = array();
	
	public function setProviderPriceList($providerPriceList)
	{
		$this->providerPriceList = $providerPriceList;
		$this->apiParas["provider_price_list"] = $providerPriceList;
	}

	public function getProviderPriceList()
	{
		return $this->providerPriceList;
	}

	public function setServiceCode($serviceCode)
	{
		$this->serviceCode = $serviceCode;
		$this->apiParas["service_code"] = $serviceCode;
	}

	public function getServiceCode()
	{
		return $this->serviceCode;
	}

	public function getApiMethodName()
	{
		return "tmall.mallitemcenter.supplier.price.upload";
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
