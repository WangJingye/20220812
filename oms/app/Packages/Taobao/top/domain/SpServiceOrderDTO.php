<?php

/**
 * 服务单列表数据
 * @author auto create
 */
class SpServiceOrderDTO
{
	
	/** 
	 * 履约类型:1, "到店"2, "到家"3, "寄送"
	 **/
	public $fulfil_type_code;
	
	/** 
	 * 生效日期
	 **/
	public $gmt_effect;
	
	/** 
	 * 过期日期
	 **/
	public $gmt_expire;
	
	/** 
	 * 服务单id
	 **/
	public $id;
	
	/** 
	 * 服务定义
	 **/
	public $service_definition_d_t_o;
	
	/** 
	 * 服务提供者
	 **/
	public $service_provider_d_t_o;
	
	/** 
	 * 服务单申请工单的幂等键
	 **/
	public $service_sequence;
	
	/** 
	 * 服务交易订单
	 **/
	public $service_trade_order_d_t_o;	
}
?>