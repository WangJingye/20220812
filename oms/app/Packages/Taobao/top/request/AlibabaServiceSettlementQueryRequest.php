<?php
/**
 * TOP API: alibaba.service.settlement.query request
 * 
 * @author auto create
 * @since 1.0, 2019.05.17
 */
class AlibabaServiceSettlementQueryRequest
{
	/** 
	 * 当前页面，开始值为1
	 **/
	private $currentPage;
	
	/** 
	 * 账单查询结束时间，时间区间限制未15分钟。 格式示例 2019-03-26 17:15:28
	 **/
	private $gmtCreateEnd;
	
	/** 
	 * 账单查询开始时间。格式示例 2019-03-26 17:15:28
	 **/
	private $gmtCreateStart;
	
	/** 
	 * 页面展示条数大小
	 **/
	private $pageSize;
	
	private $apiParas = array();
	
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

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		$this->apiParas["page_size"] = $pageSize;
	}

	public function getPageSize()
	{
		return $this->pageSize;
	}

	public function getApiMethodName()
	{
		return "alibaba.service.settlement.query";
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
