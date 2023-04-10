<?php
/**
 * TOP API: tmall.servicecenter.reservecond.delete request
 * 
 * @author auto create
 * @since 1.0, 2019.06.12
 */
class TmallServicecenterReservecondDeleteRequest
{
	/** 
	 * 主动预约条件删除
	 **/
	private $reserveOpenConditionDelDto;
	
	private $apiParas = array();
	
	public function setReserveOpenConditionDelDto($reserveOpenConditionDelDto)
	{
		$this->reserveOpenConditionDelDto = $reserveOpenConditionDelDto;
		$this->apiParas["reserve_open_condition_del_dto"] = $reserveOpenConditionDelDto;
	}

	public function getReserveOpenConditionDelDto()
	{
		return $this->reserveOpenConditionDelDto;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.reservecond.delete";
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
