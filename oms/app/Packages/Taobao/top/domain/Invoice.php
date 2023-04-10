<?php

/**
 * 发票信息
 * @author auto create
 */
class Invoice
{
	
	/** 
	 * 发票总金额(填写的条件是:invoiceFlag为Y)
	 **/
	public $amount;
	
	/** 
	 * 奇门仓储字段,说明,string(50),,
	 **/
	public $code;
	
	/** 
	 * 发票内容(不推荐使用)
	 **/
	public $content;
	
	/** 
	 * 当content和detail同时存在时，优先处理detail的信息
	 **/
	public $detail;
	
	/** 
	 * 发票抬头(填写的条件是:invoiceFlag为Y)
	 **/
	public $header;
	
	/** 
	 * 奇门仓储字段,说明,string(50),,
	 **/
	public $invoice_amount;
	
	/** 
	 * 奇门仓储字段,说明,string(50),,
	 **/
	public $invoice_content;
	
	/** 
	 * 奇门仓储字段,说明,string(50),,
	 **/
	public $invoice_head;
	
	/** 
	 * 奇门仓储字段,说明,string(50),,
	 **/
	public $number;
	
	/** 
	 * 备注
	 **/
	public $remark;
	
	/** 
	 * 税号
	 **/
	public $tax_number;
	
	/** 
	 * 发票类型(INVOICE=普通发票;VINVOICE=增值税普通发票;EVINVOICE=电子增票;填写的 条件 是:invoiceFlag为Y)
	 **/
	public $type;	
}
?>