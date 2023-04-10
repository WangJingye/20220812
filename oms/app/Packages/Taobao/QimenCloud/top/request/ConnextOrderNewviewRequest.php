<?php
/**
 * TOP API: 154o3iz55l.connext.order.newview request
 * 
 * @author auto create
 * @since 1.0, 2019.11.08
 */
class ConnextOrderNewviewRequest
{
	/** 
	 * 查询订单的结束时间
	 **/
	private $endTime;
	
	/** 
	 * 页码
	 **/
	private $page;
	
	/** 
	 * 每页大小
	 **/
	private $size;
	
	/** 
	 * 查询订单的开始时间
	 **/
	private $startTime;
	
	private $apiParas = array();
	
	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
		$this->apiParas["EndTime"] = $endTime;
	}

	public function getEndTime()
	{
		return $this->endTime;
	}

	public function setPage($page)
	{
		$this->page = $page;
		$this->apiParas["Page"] = $page;
	}

	public function getPage()
	{
		return $this->page;
	}

	public function setSize($size)
	{
		$this->size = $size;
		$this->apiParas["Size"] = $size;
	}

	public function getSize()
	{
		return $this->size;
	}

	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
		$this->apiParas["StartTime"] = $startTime;
	}

	public function getStartTime()
	{
		return $this->startTime;
	}

	public function getApiMethodName()
	{
		return "154o3iz55l.connext.order.newview";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->endTime,"endTime");
		RequestCheckUtil::checkNotNull($this->page,"page");
		RequestCheckUtil::checkNotNull($this->size,"size");
		RequestCheckUtil::checkNotNull($this->startTime,"startTime");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
