<?php
/**
 * TOP API: qimen.taobao.crm.order.detail.get request
 * 
 * @author auto create
 * @since 1.0, 2019.03.27
 */
class TaobaoCrmOrderDetailGetRequest
{
	/** 
	 * 路由参数
	 **/
	private $customerid;
	
	/** 
	 * 查询结束时间： 2000-11-30 23:59:59
	 **/
	private $endModified;
	
	/** 
	 * 扩展属性
	 **/
	private $extendProps;
	
	/** 
	 * 支持的传入字段：order_id,order_sn,deal_code,pay_status,order_status,shipping_status,lylx,shipping_code,shipping_sn,shipping_fee,sd_id,lastchanged,is_split,split_new_orders,is_combine,combine_new_orders,order_msg,fhck_id,shipping_time_fh,qd_id,tbfx_id,ori_deal_code,user_name,receiver_name,receiver_address,receiver_tel,receiver_mobile,shipping_name,seller_msg,weigh,goods_sn,sku,color_id,size_id,goods_number,brand_id
	 **/
	private $fields;
	
	/** 
	 * 订单编号
	 **/
	private $orderSn;
	
	/** 
	 * 页码: 取值范围:大于零的整数; 默认值:1
	 **/
	private $pageNo;
	
	/** 
	 * 每页条数。取值范围:大于零的整数; 默认值:20;最大值:100
	 **/
	private $pageSize;
	
	/** 
	 * 店铺代码
	 **/
	private $sdCode;
	
	/** 
	 * 查询起始时间： 2011-09-01 00:00:00
	 **/
	private $startModified;
	
	private $apiParas = array();
	
	public function setCustomerid($customerid)
	{
		$this->customerid = $customerid;
		$this->apiParas["customerid"] = $customerid;
	}

	public function getCustomerid()
	{
		return $this->customerid;
	}

	public function setEndModified($endModified)
	{
		$this->endModified = $endModified;
		$this->apiParas["endModified"] = $endModified;
	}

	public function getEndModified()
	{
		return $this->endModified;
	}

	public function setExtendProps($extendProps)
	{
		$this->extendProps = $extendProps;
		$this->apiParas["extendProps"] = $extendProps;
	}

	public function getExtendProps()
	{
		return $this->extendProps;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setOrderSn($orderSn)
	{
		$this->orderSn = $orderSn;
		$this->apiParas["orderSn"] = $orderSn;
	}

	public function getOrderSn()
	{
		return $this->orderSn;
	}

	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
		$this->apiParas["pageNo"] = $pageNo;
	}

	public function getPageNo()
	{
		return $this->pageNo;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		$this->apiParas["pageSize"] = $pageSize;
	}

	public function getPageSize()
	{
		return $this->pageSize;
	}

	public function setSdCode($sdCode)
	{
		$this->sdCode = $sdCode;
		$this->apiParas["sd_code"] = $sdCode;
	}

	public function getSdCode()
	{
		return $this->sdCode;
	}

	public function setStartModified($startModified)
	{
		$this->startModified = $startModified;
		$this->apiParas["startModified"] = $startModified;
	}

	public function getStartModified()
	{
		return $this->startModified;
	}

	public function getApiMethodName()
	{
		return "qimen.taobao.crm.order.detail.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->endModified,"endModified");
		RequestCheckUtil::checkMaxLength($this->endModified,64,"endModified");
		RequestCheckUtil::checkNotNull($this->fields,"fields");
		RequestCheckUtil::checkMaxLength($this->fields,255,"fields");
		RequestCheckUtil::checkMaxLength($this->orderSn,64,"orderSn");
		RequestCheckUtil::checkNotNull($this->pageNo,"pageNo");
		RequestCheckUtil::checkNotNull($this->pageSize,"pageSize");
		RequestCheckUtil::checkMaxLength($this->sdCode,64,"sdCode");
		RequestCheckUtil::checkNotNull($this->startModified,"startModified");
		RequestCheckUtil::checkMaxLength($this->startModified,64,"startModified");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
