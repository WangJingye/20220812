<?php
/**
 * TOP API: qimen.taobao.crm.goods.list.get request
 * 
 * @author auto create
 * @since 1.0, 2019.03.29
 */
class TaobaoCrmGoodsListGetRequest
{
	/** 
	 * 路由参数
	 **/
	private $customerid;
	
	/** 
	 * 查询结束时间
	 **/
	private $endModified;
	
	/** 
	 * 扩展属性
	 **/
	private $extendProps;
	
	/** 
	 * 支持的传入字段 goodsSn,goodsName,sizeRangeCode,sizeRangeName,brandCode,brandName,catCode,catName,topCatCode,topCatName,seriesCode,seriesName,yearCode,yearName,seasonCode,seasonName,dwCode,dwName,goodsWeight,marketPrice,shopPrice,ckj,cbj,qdtjd,qdtjd_start_time,qdtjd_end_time,ghsCode,ghsName,goodsDesc,created,modified,is_delete,goods_id
	 **/
	private $fields;
	
	/** 
	 * 页码: 取值范围:大于零的整数
	 **/
	private $pageNo;
	
	/** 
	 * 每页条数。取值范围:大于零的整数; 默认值:20;最大值:100
	 **/
	private $pageSize;
	
	/** 
	 * 查询起始时间
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
		return "qimen.taobao.crm.goods.list.get";
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
		RequestCheckUtil::checkNotNull($this->startModified,"startModified");
		RequestCheckUtil::checkMaxLength($this->startModified,64,"startModified");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
