<?php

/**
 * 请求参数u
 * @author auto create
 */
class WorkcardCreateRequest
{
	
	/** 
	 * 业务code
	 **/
	public $biz_code;
	
	/** 
	 * 申请次数
	 **/
	public $service_count;
	
	/** 
	 * 服务提供者
	 **/
	public $service_provider;
	
	/** 
	 * 申请工单时的序号，对应服务单上的serviceSequence。用于控制幂等，防重复提交
	 **/
	public $service_sequence;
	
	/** 
	 * 服务单id
	 **/
	public $sp_service_order_id;	
}
?>