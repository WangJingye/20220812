<?php

/**
 * 主动预约开通条件
 * @author auto create
 */
class ReserveOpenConditionDTO
{
	
	/** 
	 * 区域ids集合
	 **/
	public $area_ids;
	
	/** 
	 * 品牌id
	 **/
	public $brand_id;
	
	/** 
	 * 类目id
	 **/
	public $category_id;
	
	/** 
	 * 城市id
	 **/
	public $city_id;
	
	/** 
	 * 排除的区域id
	 **/
	public $exclude_area_ids;
	
	/** 
	 * 身份id
	 **/
	public $province_id;
	
	/** 
	 * 服务code
	 **/
	public $service_code;	
}
?>