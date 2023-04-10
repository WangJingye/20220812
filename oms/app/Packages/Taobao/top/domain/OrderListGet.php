<?php

/**
 * 列表详情
 * @author auto create
 */
class OrderListGet
{
	
	/** 
	 * 备注
	 **/
	public $bz;
	
	/** 
	 * 单据编号
	 **/
	public $djbh;
	
	/** 
	 * 消费类型
	 **/
	public $djlx;
	
	/** 
	 * 关联单据编号
	 **/
	public $dtdh;
	
	/** 
	 * 关联交易号
	 **/
	public $dtjyh;
	
	/** 
	 * 扩展字段
	 **/
	public $extend_props;
	
	/** 
	 * 单据类型(订单1，退单0)
	 **/
	public $is_order;
	
	/** 
	 * 支付方式编号
	 **/
	public $pay_id;
	
	/** 
	 * 金额
	 **/
	public $price;
	
	/** 
	 * 渠道代码
	 **/
	public $qd_id;
	
	/** 
	 * 添加时间
	 **/
	public $rq;
	
	/** 
	 * 店铺代码
	 **/
	public $sd_id;	
}
?>