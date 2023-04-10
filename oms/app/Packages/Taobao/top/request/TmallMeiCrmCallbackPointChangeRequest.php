<?php
/**
 * TOP API: tmall.mei.crm.callback.point.change request
 * 
 * @author auto create
 * @since 1.0, 2018.07.26
 */
class TmallMeiCrmCallbackPointChangeRequest
{
	/** 
	 * 处理失败的错误码.
	 **/
	private $errorCode;
	
	/** 
	 * 拓展信息
	 **/
	private $extInfo;
	
	/** 
	 * 混淆会员手机号码
	 **/
	private $mixMobile;
	
	/** 
	 * 积分信息
	 **/
	private $point;
	
	/** 
	 * 变更记录ID
	 **/
	private $recordId;
	
	/** 
	 * 0:成功。1：失败
	 **/
	private $result;
	
	private $apiParas = array();
	
	public function setErrorCode($errorCode)
	{
		$this->errorCode = $errorCode;
		$this->apiParas["error_code"] = $errorCode;
	}

	public function getErrorCode()
	{
		return $this->errorCode;
	}

	public function setExtInfo($extInfo)
	{
		$this->extInfo = $extInfo;
		$this->apiParas["ext_info"] = $extInfo;
	}

	public function getExtInfo()
	{
		return $this->extInfo;
	}

	public function setMixMobile($mixMobile)
	{
		$this->mixMobile = $mixMobile;
		$this->apiParas["mix_mobile"] = $mixMobile;
	}

	public function getMixMobile()
	{
		return $this->mixMobile;
	}

	public function setPoint($point)
	{
		$this->point = $point;
		$this->apiParas["point"] = $point;
	}

	public function getPoint()
	{
		return $this->point;
	}

	public function setRecordId($recordId)
	{
		$this->recordId = $recordId;
		$this->apiParas["record_id"] = $recordId;
	}

	public function getRecordId()
	{
		return $this->recordId;
	}

	public function setResult($result)
	{
		$this->result = $result;
		$this->apiParas["result"] = $result;
	}

	public function getResult()
	{
		return $this->result;
	}

	public function getApiMethodName()
	{
		return "tmall.mei.crm.callback.point.change";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->recordId,"recordId");
		RequestCheckUtil::checkNotNull($this->result,"result");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
