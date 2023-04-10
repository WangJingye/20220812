<?php
/**
 * TOP API: tmall.servicecenter.workcard.query request
 * 
 * @author auto create
 * @since 1.0, 2020.01.09
 */
class TmallServicecenterWorkcardQueryRequest
{
	/** 
	 * 淘宝交易订单号。主订单或子订单均可
	 **/
	private $bizOrderId;
	
	/** 
	 * 当前页数
	 **/
	private $currentPage;
	
	/** 
	 * 工单创建结束时间，必须与工单创建开始时间一起传入，且间隔不超过15分钟
	 **/
	private $gmtCreateEnd;
	
	/** 
	 * 工单创建开始时间
	 **/
	private $gmtCreateStart;
	
	/** 
	 * 工单id
	 **/
	private $id;
	
	/** 
	 * 核销码
	 **/
	private $identifyCode;
	
	/** 
	 * 每页大小
	 **/
	private $pageSize;
	
	/** 
	 * 门店/网点id
	 **/
	private $serviceStoreId;
	
	private $apiParas = array();
	
	public function setBizOrderId($bizOrderId)
	{
		$this->bizOrderId = $bizOrderId;
		$this->apiParas["biz_order_id"] = $bizOrderId;
	}

	public function getBizOrderId()
	{
		return $this->bizOrderId;
	}

	public function setCurrentPage($currentPage)
	{
		$this->currentPage = $currentPage;
		$this->apiParas["current_page"] = $currentPage;
	}

	public function getCurrentPage()
	{
		return $this->currentPage;
	}

	public function setGmtCreateEnd($gmtCreateEnd)
	{
		$this->gmtCreateEnd = $gmtCreateEnd;
		$this->apiParas["gmt_create_end"] = $gmtCreateEnd;
	}

	public function getGmtCreateEnd()
	{
		return $this->gmtCreateEnd;
	}

	public function setGmtCreateStart($gmtCreateStart)
	{
		$this->gmtCreateStart = $gmtCreateStart;
		$this->apiParas["gmt_create_start"] = $gmtCreateStart;
	}

	public function getGmtCreateStart()
	{
		return $this->gmtCreateStart;
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

	public function setIdentifyCode($identifyCode)
	{
		$this->identifyCode = $identifyCode;
		$this->apiParas["identify_code"] = $identifyCode;
	}

	public function getIdentifyCode()
	{
		return $this->identifyCode;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		$this->apiParas["page_size"] = $pageSize;
	}

	public function getPageSize()
	{
		return $this->pageSize;
	}

	public function setServiceStoreId($serviceStoreId)
	{
		$this->serviceStoreId = $serviceStoreId;
		$this->apiParas["service_store_id"] = $serviceStoreId;
	}

	public function getServiceStoreId()
	{
		return $this->serviceStoreId;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.workcard.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
