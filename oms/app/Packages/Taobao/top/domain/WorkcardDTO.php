<?php

/**
 * 工单列表
 * @author auto create
 */
class WorkcardDTO
{
	
	/** 
	 * 额外属性
	 **/
	public $attributes;
	
	/** 
	 * 买家信息
	 **/
	public $buyer;
	
	/** 
	 * 买家期望服务时间
	 **/
	public $buyer_expect_date;
	
	/** 
	 * 服务履约类型
	 **/
	public $fulfil_type_code;
	
	/** 
	 * 取消时间
	 **/
	public $gmt_cancel;
	
	/** 
	 * 创建时间
	 **/
	public $gmt_create;
	
	/** 
	 * 核销时间
	 **/
	public $gmt_identify;
	
	/** 
	 * 修改时间
	 **/
	public $gmt_modify;
	
	/** 
	 * 预约结束时间
	 **/
	public $gmt_reserve_end;
	
	/** 
	 * 预约时间开始
	 **/
	public $gmt_reserve_start;
	
	/** 
	 * 签到时间
	 **/
	public $gmt_sign_in;
	
	/** 
	 * 工单id
	 **/
	public $id;
	
	/** 
	 * 剩余次数
	 **/
	public $left_service_count;
	
	/** 
	 * 实物订单信息
	 **/
	public $master_trade_order;
	
	/** 
	 * 主订单号
	 **/
	public $parent_biz_order_id;
	
	/** 
	 * 预约失败编码
	 **/
	public $reserve_fail_code;
	
	/** 
	 * 预约失败描述
	 **/
	public $reserve_fail_desc;
	
	/** 
	 * 工单服务总次数
	 **/
	public $service_count;
	
	/** 
	 * 服务定义
	 **/
	public $service_definition;
	
	/** 
	 * 服务提供者
	 **/
	public $service_provider;
	
	/** 
	 * 服务订单数据
	 **/
	public $service_trade_order;
	
	/** 
	 * 工单状态编码
	 **/
	public $status_code;
	
	/** 
	 * 使用次数
	 **/
	public $used_service_count;	
}
?>