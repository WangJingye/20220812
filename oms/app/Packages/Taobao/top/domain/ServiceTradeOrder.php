<?php

/**
 * 服务订单数据
 * @author auto create
 */
class ServiceTradeOrder
{
	
	/** 
	 * 服务商品额外属性
	 **/
	public $attributes;
	
	/** 
	 * 服务商品的id
	 **/
	public $auction_id;
	
	/** 
	 * 服务商品的标题
	 **/
	public $auction_title;
	
	/** 
	 * 购买数量
	 **/
	public $buy_amount;
	
	/** 
	 * 服务商品的类目
	 **/
	public $category_id;
	
	/** 
	 * 付款时间
	 **/
	public $gmt_pay;
	
	/** 
	 * 订单id
	 **/
	public $order_id;
	
	/** 
	 * 服务商品的商家编码
	 **/
	public $outer_id_sku;
	
	/** 
	 * 服务商品的采购价。单位为分
	 **/
	public $purchase_price_unit;
	
	/** 
	 * 服务商品的卖家昵称
	 **/
	public $seller_nick;
	
	/** 
	 * 服务商品的店铺名
	 **/
	public $shop_name;
	
	/** 
	 * 服务商品的sku描述
	 **/
	public $sku_desc;
	
	/** 
	 * 服务商品的sku id
	 **/
	public $sku_id;	
}
?>