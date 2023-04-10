<?php

/**
 * 结算明细list
 * @author auto create
 */
class Datalist
{
	
	/** 
	 * 触发本次打款的动作类型。serviceOrder-tradeSuccess: 商家扣款到中间账户；serviceOrder-cancel: 退款;huijin-commision: 门店抽佣;huijin-store-transfer: 转账给门店
	 **/
	public $action;
	
	/** 
	 * 支付宝交易订单号
	 **/
	public $alipay_order_id;
	
	/** 
	 * 扩展信息；json格式
	 **/
	public $attributes;
	
	/** 
	 * 打款备注
	 **/
	public $comment;
	
	/** 
	 * 创建时间，单位毫秒
	 **/
	public $create_time;
	
	/** 
	 * 货币类型 人民币(CNY)
	 **/
	public $currency;
	
	/** 
	 * 账单ID
	 **/
	public $id;
	
	/** 
	 * 入款方支付宝账号
	 **/
	public $in_user_alipay_account_id;
	
	/** 
	 * 入款方nick
	 **/
	public $in_user_nick;
	
	/** 
	 * 入款方用户角色 BUYER:买家 SELLER:卖家, STORE：门店, TP：服务商, PLATFORM：平台
	 **/
	public $in_user_role;
	
	/** 
	 * 交易实物订单号
	 **/
	public $master_trade_order_id;
	
	/** 
	 * 出款方支付宝账号
	 **/
	public $out_user_alipay_account_id;
	
	/** 
	 * 出款方nick
	 **/
	public $out_user_nick;
	
	/** 
	 * 出款方用户角色 BUYER:买家 SELLER:卖家, STORE：门店, TP：服务商, PLATFORM：平台
	 **/
	public $out_user_role;
	
	/** 
	 * 交易主订单号
	 **/
	public $parent_trade_order_id;
	
	/** 
	 * 创建时间，单位毫秒
	 **/
	public $pay_time;
	
	/** 
	 * 卖家nick
	 **/
	public $seller_nick;
	
	/** 
	 * 服务单号
	 **/
	public $service_order_id;
	
	/** 
	 * 门店Code
	 **/
	public $service_store_code;
	
	/** 
	 * 门店Id
	 **/
	public $service_store_id;
	
	/** 
	 * 门店名称
	 **/
	public $service_store_name;
	
	/** 
	 * 交易服务子订单ID
	 **/
	public $service_trade_order_id;
	
	/** 
	 * 状态；销帐：FINISH ;未销账:ONGOING
	 **/
	public $status;
	
	/** 
	 * 转帐金额，单位分
	 **/
	public $transfer_amount;
	
	/** 
	 * 工单ID
	 **/
	public $workcard_id;
	
	/** 
	 * 工单多次作业时的批次号
	 **/
	public $workcard_sequence;	
}
?>