<?php

/**
 * 实物订单信息
 * @author auto create
 */
class MasterTradeOrder
{
	
	/** 
	 * 商品额外属性
	 **/
	public $attributes;
	
	/** 
	 * 商品id
	 **/
	public $auction_id;
	
	/** 
	 * 商品标题
	 **/
	public $auction_title;
	
	/** 
	 * 品牌id
	 **/
	public $brand_id;
	
	/** 
	 * 购买数量
	 **/
	public $buy_amount;
	
	/** 
	 * 类目id
	 **/
	public $category_id;
	
	/** 
	 * 签收时间
	 **/
	public $gmt_arrival;
	
	/** 
	 * 预计签收时间
	 **/
	public $gmt_expect_arrival;
	
	/** 
	 * 付款时间
	 **/
	public $gmt_pay;
	
	/** 
	 * 发货时间
	 **/
	public $gmt_shipped;
	
	/** 
	 * 订单id
	 **/
	public $order_id;
	
	/** 
	 * 商品编码
	 **/
	public $outer_id_p;
	
	/** 
	 * 商家编码
	 **/
	public $outer_id_sku;
	
	/** 
	 * 价格。单位为分
	 **/
	public $price;
	
	/** 
	 * 卖家昵称
	 **/
	public $seller_nick;
	
	/** 
	 * 店铺名
	 **/
	public $shop_name;
	
	/** 
	 * sku描述
	 **/
	public $sku_desc;
	
	/** 
	 * sku id
	 **/
	public $sku_id;
	
	/** 
	 * spu id
	 **/
	public $spu_id;	
}
?>