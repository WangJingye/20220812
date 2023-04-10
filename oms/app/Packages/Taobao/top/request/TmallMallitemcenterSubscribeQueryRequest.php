<?php
/**
 * TOP API: tmall.mallitemcenter.subscribe.query request
 * 
 * @author auto create
 * @since 1.0, 2019.03.29
 */
class TmallMallitemcenterSubscribeQueryRequest
{
	/** 
	 * 入参query
	 **/
	private $query;
	
	private $apiParas = array();
	
	public function setQuery($query)
	{
		$this->query = $query;
		$this->apiParas["query"] = $query;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function getApiMethodName()
	{
		return "tmall.mallitemcenter.subscribe.query";
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
