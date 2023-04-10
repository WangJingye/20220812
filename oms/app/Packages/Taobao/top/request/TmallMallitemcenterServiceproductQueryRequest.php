<?php
/**
 * TOP API: tmall.mallitemcenter.serviceproduct.query request
 * 
 * @author auto create
 * @since 1.0, 2019.03.29
 */
class TmallMallitemcenterServiceproductQueryRequest
{
	/** 
	 * 服务产品id
	 **/
	private $id;
	
	/** 
	 * 服务名称
	 **/
	private $serviceCode;
	
	/** 
	 * 产品类型
	 **/
	private $serviceProductType;
	
	/** 
	 * 产品状态
	 **/
	private $status;
	
	private $apiParas = array();
	
	public function setId($id)
	{
		$this->id = $id;
		$this->apiParas["id"] = $id;
	}

	public function getId()
	{
		return $this->id;
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

	public function setServiceProductType($serviceProductType)
	{
		$this->serviceProductType = $serviceProductType;
		$this->apiParas["service_product_type"] = $serviceProductType;
	}

	public function getServiceProductType()
	{
		return $this->serviceProductType;
	}

	public function setStatus($status)
	{
		$this->status = $status;
		$this->apiParas["status"] = $status;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getApiMethodName()
	{
		return "tmall.mallitemcenter.serviceproduct.query";
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
