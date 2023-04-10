<?php
/**
 * TOP API: tmall.servicecenter.servicestore.updatestatus request
 * 
 * @author auto create
 * @since 1.0, 2019.03.27
 */
class TmallServicecenterServicestoreUpdatestatusRequest
{
	/** 
	 * 业务类型。不同业务传不同的值
	 **/
	private $bizType;
	
	/** 
	 * 门店id
	 **/
	private $id;
	
	/** 
	 * 状态。1 营业，0歇业，-1彻底关店
	 **/
	private $status;
	
	private $apiParas = array();
	
	public function setBizType($bizType)
	{
		$this->bizType = $bizType;
		$this->apiParas["biz_type"] = $bizType;
	}

	public function getBizType()
	{
		return $this->bizType;
	}

	public function setId($id)
	{
		$this->id = $id;
		$this->apiParas["id"] = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setStatus($status)
	{
		$this->status = $status;
		$this->apiParas["status"] = $status;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.servicestore.updatestatus";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->bizType,"bizType");
		RequestCheckUtil::checkNotNull($this->id,"id");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
