<?php

/**
 * 订单处理信息
 * @author auto create
 */
class Process
{
	
	/** 
	 * 运单号
	 **/
	public $express_code;
	
	/** 
	 * 操作内容
	 **/
	public $operate_info;
	
	/** 
	 * 当前状态操作时间(YYYY-MM-DD HH:MM:SS)
	 **/
	public $operate_time;
	
	/** 
	 * 当前状态操作员编码
	 **/
	public $operator_code;
	
	/** 
	 * 当前状态操作员姓名
	 **/
	public $operator_name;
	
	/** 
	 * 单据状态(ACCEPT=仓库接单;PARTFULFILLED-部分收货完成;FULFILLED=收货完成;PRINT = 打印;PICK=捡货;CHECK = 复核 ;PACKAGE= 打包;WEIGH= 称重;READY=待提货;DELIVERED=已发货;REFUSE=买家拒签;EXCEPTION =异常;CLOSED= 关闭;CANCELED= 取 消;REJECT=仓库拒单;SIGN=签收;TMSCANCELED=快递拦截;OTHER=其他;PARTDELIVERED=部分发货完成;TMSCANCELFAILED=快递拦截失败;只传英 文编码)
	 **/
	public $process_status;
	
	/** 
	 * 备注
	 **/
	public $remark;	
}
?>