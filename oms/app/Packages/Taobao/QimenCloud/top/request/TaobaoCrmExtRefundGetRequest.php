<?php
/**
 * TOP API: qimen.taobao.crm.ext.refund.get request
 * 
 * @author auto create
 * @since 1.0, 2018.12.28
 */
class TaobaoCrmExtRefundGetRequest
{
	/** 
	 * customerid
	 **/
	private $customerid;
	
	/** 
	 * 退单列表修改结束时间
	 **/
	private $endModified;
	
	/** 
	 * 扩展属性
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
	 * 商店编码
	 **/
	private $sdCode;
	
	/** 
	 * 退单列表修改开始时间
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
		return "qimen.taobao.crm.ext.refund.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->customerid,"customerid");
		RequestCheckUtil::checkMaxLength($this->customerid,255,"customerid");
		RequestCheckUtil::checkNotNull($this->endModified,"endModified");
		RequestCheckUtil::checkMaxLength($this->endModified,64,"endModified");
		RequestCheckUtil::checkNotNull($this->pageNo,"pageNo");
		RequestCheckUtil::checkMaxLength($this->pageNo,11,"pageNo");
		RequestCheckUtil::checkNotNull($this->pageSize,"pageSize");
		RequestCheckUtil::checkMaxLength($this->pageSize,11,"pageSize");
		RequestCheckUtil::checkMaxLength($this->sdCode,64,"sdCode");
		RequestCheckUtil::checkNotNull($this->startModified,"startModified");
		RequestCheckUtil::checkMaxLength($this->startModified,64,"startModified");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
