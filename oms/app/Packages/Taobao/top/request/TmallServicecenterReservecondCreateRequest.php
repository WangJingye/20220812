<?php
/**
 * TOP API: tmall.servicecenter.reservecond.create request
 * 
 * @author auto create
 * @since 1.0, 2019.06.12
 */
class TmallServicecenterReservecondCreateRequest
{
	/** 
	 * 主动预约开通条件
	 **/
	private $rocList;
	
	private $apiParas = array();
	
	public function setRocList($rocList)
	{
		$this->rocList = $rocList;
		$this->apiParas["roc_list"] = $rocList;
	}

	public function getRocList()
	{
		return $this->rocList;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.reservecond.create";
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
