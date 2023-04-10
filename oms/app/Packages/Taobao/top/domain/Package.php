<?php

/**
 * 包裹详情
 * @author auto create
 */
class Package
{
	
	/** 
	 * 运单号, string (50) , 不必填
	 **/
	public $express_code;
	
	/** 
	 * 商品列表
	 **/
	public $items;
	
	/** 
	 * 承运商编码string (50) , SF=顺丰、EMS=标准快递、EYB=经济快件、ZJS=宅急送、YTO=圆通 、ZTO=中通 (ZTO) 、HTKY=百世汇通、BSKY=百世快运、UC=优速、STO=申通、TTKDEX=天天快递 、QFKD=全峰、FAST=快捷、POSTB=邮政小包 、GTO=国通、YUNDA=韵达、JD=京东配送、DD=当当宅配、AMAZON=亚马逊物流、DBWL=德邦物流、DBKD=德邦快递、DBKY=德邦快运、RRS=日日顺、OTHER=其他，必填, (只传英文编码)
	 **/
	public $logistics_code;
	
	/** 
	 * 物流公司名称, string (200)
	 **/
	public $logistics_name;
	
	/** 
	 * 备注, string (500) ,
	 **/
	public $remarks;
	
	/** 
	 * 当前状态操作时间, string (19) , YYYY-MM-DD HH:MM:SS
	 **/
	public $sign_time;
	
	/** 
	 * 签收人姓名, string (50) ，必填
	 **/
	public $sign_user_name;
	
	/** 
	 * 状态, sign-已签收string (50)
	 **/
	public $status;
	
	/** 
	 * 包裹重量 (千克) , double (18, 3)
	 **/
	public $weight;	
}
?>