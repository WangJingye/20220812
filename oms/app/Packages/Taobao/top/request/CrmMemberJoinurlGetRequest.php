<?php
/**
 * TOP API: taobao.crm.member.joinurl.get request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class CrmMemberJoinurlGetRequest
{
	/** 
	 * 回调url
	 **/
	private $callbackUrl;
	
	/** 
	 * 扩展参数为JSON字符串，用于埋点统计，source为来源字段固定值 paiyangji代表来源派样机类型设备，deviceId 为设备id，itemId 相关商品id
	 **/
	private $extraInfo;
	
	private $apiParas = array();
	
	public function setCallbackUrl($callbackUrl)
	{
		$this->callbackUrl = $callbackUrl;
		$this->apiParas["callback_url"] = $callbackUrl;
	}

	public function getCallbackUrl()
	{
		return $this->callbackUrl;
	}

	public function setExtraInfo($extraInfo)
	{
		$this->extraInfo = $extraInfo;
		$this->apiParas["extra_info"] = $extraInfo;
	}

	public function getExtraInfo()
	{
		return $this->extraInfo;
	}

	public function getApiMethodName()
	{
		return "taobao.crm.member.joinurl.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->extraInfo,"extraInfo");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
