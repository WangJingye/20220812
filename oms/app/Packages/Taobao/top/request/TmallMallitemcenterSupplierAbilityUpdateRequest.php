<?php
/**
 * TOP API: tmall.mallitemcenter.supplier.ability.update request
 * 
 * @author auto create
 * @since 1.0, 2019.08.29
 */
class TmallMallitemcenterSupplierAbilityUpdateRequest
{
	/** 
	 * 入参
	 **/
	private $param0;
	
	private $apiParas = array();
	
	public function setParam0($param0)
	{
		$this->param0 = $param0;
		$this->apiParas["param0"] = $param0;
	}

	public function getParam0()
	{
		return $this->param0;
	}

	public function getApiMethodName()
	{
		return "tmall.mallitemcenter.supplier.ability.update";
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
