<?php

/**
 * result
 * @author auto create
 */
class MemberAccountDto
{
	
	/** 
	 * bindStatus 1：绑卡（已经是线下会员线上未绑定，或者解绑后再绑定），2：注册
	 **/
	public $bind_status;
	
	/** 
	 * gmtCreate
	 **/
	public $gmt_create;
	
	/** 
	 * 等级编号
	 **/
	public $grade;
	
	/** 
	 * 等级名称
	 **/
	public $grade_name;	
}
?>