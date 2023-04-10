<?php
/**
 * TOP API: tmall.servicecenter.spserviceorder.query request
 * 
 * @author auto create
 * @since 1.0, 2019.11.08
 */
class TmallServicecenterSpserviceorderQueryRequest
{
	/** 
	 * 交易主订单id
	 **/
	private $parentBizOrderId;
	
	private $apiParas = array();
	
	public function setParentBizOrderId($parentBizOrderId)
	{
		$this->parentBizOrderId = $parentBizOrderId;
		$this->apiParas["parent_biz_order_id"] = $parentBizOrderId;
	}

	public function getParentBizOrderId()
	{
		return $this->parentBizOrderId;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.spserviceorder.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->parentBizOrderId,"parentBizOrderId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
