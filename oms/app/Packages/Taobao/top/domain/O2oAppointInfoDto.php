<?php

/**
 * 返回的预约信息，多个预约信息按照预约时间升序排序
 * @author auto create
 */
class O2oAppointInfoDto
{
	
	/** 
	 * 预约信息唯一编码，如果使用此参数，customer_nick和mall_code参数不会使用
	 **/
	public $appoint_code;
	
	/** 
	 * 预约属性信息列表
	 **/
	public $appoint_info_params;	
}
?>