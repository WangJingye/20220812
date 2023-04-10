<?php

/**
 * 发货要求列表
 * @author auto create
 */
class DeliveryRequirements
{
	
	/** 
	 * 发货服务类型(PTPS:普通配送;LLPS:冷链配送;HBP:环保配)
	 **/
	public $delivery_type;
	
	/** 
	 * 备注
	 **/
	public $remark;
	
	/** 
	 * 要求送达日期(YYYY-MM-DD)
	 **/
	public $schedule_day;
	
	/** 
	 * 投递时间范围要求(结束时间;格式：HH:MM:SS)
	 **/
	public $schedule_end_time;
	
	/** 
	 * 投递时间范围要求(开始时间;格式：HH:MM:SS)
	 **/
	public $schedule_start_time;
	
	/** 
	 * 投递时延要求(1=工作日;2=节假日;101=当日达;102=次晨达;103=次日达;104=预约达;105=隔日达)
	 **/
	public $schedule_type;	
}
?>