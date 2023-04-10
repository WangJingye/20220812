<?php
/**
 * TOP API: qimen.taobao.crm.order.returndetail.get request
 * 
 * @author auto create
 * @since 1.0, 2018.12.28
 */
class TaobaoCrmOrderReturndetailGetRequest
{
	/** 
	 * 退单列表修改时间的结束时间，格式为 2013-11-12 12:00:00
	 **/
	private $endModified;
	
	/** 
	 * 扩展字段
	 **/
	private $extendProps;
	
	/** 
	 * 页数
	 **/
	private $pageNo;
	
	/** 
	 * 页码
	 **/
	private $pageSize;
	
	/** 
	 * E3退单编号
	 **/
	private $returnOrderSn;
	
	/** 
	 * E3商店编码
	 **/
	private $sdCode;
	
	/** 
	 * 退单列表修改时间开始时间，格式为  2013-11-12 12:00:00
	 **/
	private $startModified;
	
	private $apiParas = array();
	
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

	public function setReturnOrderSn($returnOrderSn)
	{
		$this->returnOrderSn = $returnOrderSn;
		$this->apiParas["return_order_sn"] = $returnOrderSn;
	}

	public function getReturnOrderSn()
	{
		return $this->returnOrderSn;
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
		return "qimen.taobao.crm.order.returndetail.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->endModified,"endModified");
		RequestCheckUtil::checkNotNull($this->pageNo,"pageNo");
		RequestCheckUtil::checkNotNull($this->pageSize,"pageSize");
		RequestCheckUtil::checkMaxLength($this->returnOrderSn,64,"returnOrderSn");
		RequestCheckUtil::checkMaxLength($this->sdCode,64,"sdCode");
		RequestCheckUtil::checkNotNull($this->startModified,"startModified");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
