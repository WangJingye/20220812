<?php
/**
 * TOP API: tmall.mei.crm.member.sync request
 * 
 * @author auto create
 * @since 1.0, 2019.08.21
 */
class TmallMeiCrmMemberSyncRequest
{
	/** 
	 * 会员拓展信息
	 **/
	private $extend;
	
	/** 
	 * 会员等级
	 **/
	private $level;
	
	/** 
	 * 混淆昵称
	 **/
	private $mixNick;
	
	/** 
	 * 会员手机号码
	 **/
	private $mobile;
	
	/** 
	 * 昵称
	 **/
	private $nick;
	
	/** 
	 * 会员积分
	 **/
	private $point;
	
	/** 
	 * 该次同步的版本信息（建议使用时间戳）
	 **/
	private $version;
	
	private $apiParas = array();
	
	public function setExtend($extend)
	{
		$this->extend = $extend;
		$this->apiParas["extend"] = $extend;
	}

	public function getExtend()
	{
		return $this->extend;
	}

	public function setLevel($level)
	{
		$this->level = $level;
		$this->apiParas["level"] = $level;
	}

	public function getLevel()
	{
		return $this->level;
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

	public function setMobile($mobile)
	{
		$this->mobile = $mobile;
		$this->apiParas["mobile"] = $mobile;
	}

	public function getMobile()
	{
		return $this->mobile;
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

	public function setPoint($point)
	{
		$this->point = $point;
		$this->apiParas["point"] = $point;
	}

	public function getPoint()
	{
		return $this->point;
	}

	public function setVersion($version)
	{
		$this->version = $version;
		$this->apiParas["version"] = $version;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function getApiMethodName()
	{
		return "tmall.mei.crm.member.sync";
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
