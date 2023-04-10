<?php

/**
 * 入参
 * @author auto create
 */
class EnableServiceStoreRequestDto
{
	
	/** 
	 * 是否启用
	 **/
	public $enable;
	
	/** 
	 * 服务编码
	 **/
	public $service_code;
	
	/** 
	 * 服务sku列表
	 **/
	public $service_sku_list;
	
	/** 
	 * 门店id
	 **/
	public $store_id;
	
	/** 
	 * 门店名称
	 **/
	public $store_name;	
}
?>