<?php
/**
 * TOP API: tmall.servicecenter.msf.tasks.update request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class TmallServicecenterMsfTasksUpdateRequest
{
	/** 
	 * 子订单号列表。最多100个
	 **/
	private $bizOrderIds;
	
	/** 
	 * 周期序号。必须与子订单列表对应
	 **/
	private $seqs;
	
	/** 
	 * 服务编码
	 **/
	private $serviceCode;
	
	/** 
	 * 调用来源
	 **/
	private $source;
	
	/** 
	 * 工人手机号
	 **/
	private $workerMobile;
	
	private $apiParas = array();
	
	public function setBizOrderIds($bizOrderIds)
	{
		$this->bizOrderIds = $bizOrderIds;
		$this->apiParas["biz_order_ids"] = $bizOrderIds;
	}

	public function getBizOrderIds()
	{
		return $this->bizOrderIds;
	}

	public function setSeqs($seqs)
	{
		$this->seqs = $seqs;
		$this->apiParas["seqs"] = $seqs;
	}

	public function getSeqs()
	{
		return $this->seqs;
	}

	public function setServiceCode($serviceCode)
	{
		$this->serviceCode = $serviceCode;
		$this->apiParas["service_code"] = $serviceCode;
	}

	public function getServiceCode()
	{
		return $this->serviceCode;
	}

	public function setSource($source)
	{
		$this->source = $source;
		$this->apiParas["source"] = $source;
	}

	public function getSource()
	{
		return $this->source;
	}

	public function setWorkerMobile($workerMobile)
	{
		$this->workerMobile = $workerMobile;
		$this->apiParas["worker_mobile"] = $workerMobile;
	}

	public function getWorkerMobile()
	{
		return $this->workerMobile;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.msf.tasks.update";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->bizOrderIds,"bizOrderIds");
		RequestCheckUtil::checkMaxListSize($this->bizOrderIds,100,"bizOrderIds");
		RequestCheckUtil::checkNotNull($this->seqs,"seqs");
		RequestCheckUtil::checkMaxListSize($this->seqs,100,"seqs");
		RequestCheckUtil::checkNotNull($this->serviceCode,"serviceCode");
		RequestCheckUtil::checkNotNull($this->source,"source");
		RequestCheckUtil::checkNotNull($this->workerMobile,"workerMobile");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
