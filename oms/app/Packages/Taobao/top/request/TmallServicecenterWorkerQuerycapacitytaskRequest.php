<?php
/**
 * TOP API: tmall.servicecenter.worker.querycapacitytask request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class TmallServicecenterWorkerQuerycapacitytaskRequest
{
	/** 
	 * 查询对象
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
		return "tmall.servicecenter.worker.querycapacitytask";
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
