<?php
/**
 * TOP API: qimen.taobao.crm.cat.list.get request
 * 
 * @author auto create
 * @since 1.0, 2018.12.28
 */
class TaobaoCrmCatListGetRequest
{
	/** 
	 * 查询内结束时间： 2000-11-3023:59:59
	 **/
	private $endModified;
	
	/** 
	 * 扩展属性
	 **/
	private $extendProps;
	
	/** 
	 * 支持的传入字段 cat_code,cat_name,cat_desc,p_id,lastchanged
	 **/
	private $fields;
	
	/** 
	 * 分类（1：顶级分类，2：二级分类）
	 **/
	private $isTop;
	
	/** 
	 * 页码: 取值范围:大于零的整数; 默认值:1
	 **/
	private $pageNo;
	
	/** 
	 * 每页条数。取值范围:大于零的整数; 默认值:20;最大值:100
	 **/
	private $pageSize;
	
	/** 
	 * 查询起始时间： 2011-09-01 00:00:00
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

	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setIsTop($isTop)
	{
		$this->isTop = $isTop;
		$this->apiParas["isTop"] = $isTop;
	}

	public function getIsTop()
	{
		return $this->isTop;
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
		return "qimen.taobao.crm.cat.list.get";
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
		RequestCheckUtil::checkMaxLength($this->fields,64,"fields");
		RequestCheckUtil::checkNotNull($this->isTop,"isTop");
		RequestCheckUtil::checkNotNull($this->pageNo,"pageNo");
		RequestCheckUtil::checkNotNull($this->pageSize,"pageSize");
		RequestCheckUtil::checkNotNull($this->startModified,"startModified");
		RequestCheckUtil::checkMaxLength($this->startModified,64,"startModified");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
