<?php
/**
 * TOP API: taobao.crm.member.identity.get request
 * 
 * @author auto create
 * @since 1.0, 2018.07.25
 */
class CrmMemberIdentityGetRequest
{
	/** 
	 * 扩展参数为JSON字符串，用于埋点统计，source为来源字段固定值 paiyangji代表来源派样机类型设备，deviceId 为设备id，itemId 相关商品id
	 **/
	private $extraInfo;
	
	/** 
	 * 混淆昵称，
	 **/
	private $mixNick;
	
	/** 
	 * 明文nick，可不填，直接填混淆昵称
	 **/
	private $nick;
	
	private $apiParas = array();
	
	public function setExtraInfo($extraInfo)
	{
		$this->extraInfo = $extraInfo;
		$this->apiParas["extra_info"] = $extraInfo;
	}

	public function getExtraInfo()
	{
		return $this->extraInfo;
	}

	public function setMixNick($mixNick)
	{
		$this->mixNick = $mixNick;
		$this->apiParas["mix_nick"] = $mixNick;
	}

	public function getMixNick()
	{
		return $this->mixNick;
	}

	public function setNick($nick)
	{
		$this->nick = $nick;
		$this->apiParas["nick"] = $nick;
	}

	public function getNick()
	{
		return $this->nick;
	}

	public function getApiMethodName()
	{
		return "taobao.crm.member.identity.get";
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
