<?php
/**
 * TOP API: tmall.servicecenter.workcard.create request
 * 
 * @author auto create
 * @since 1.0, 2019.10.22
 */
class TmallServicecenterWorkcardCreateRequest
{
	/** 
	 * 请求参数u
	 **/
	private $createRequest;
	
	private $apiParas = array();
	
	public function setCreateRequest($createRequest)
	{
		$this->createRequest = $createRequest;
		$this->apiParas["create_request"] = $createRequest;
	}

	public function getCreateRequest()
	{
		return $this->createRequest;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.workcard.create";
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
