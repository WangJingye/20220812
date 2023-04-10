<?php
/**
 * TOP API: tmall.crm.member.point.change request
 * 
 * @author auto create
 * @since 1.0, 2018.07.26
 */
class TmallCrmMemberPointChangeRequest
{
	/** 
	 * 业务代码
	 **/
	private $bizCode;
	
	/** 
	 * 业务描述
	 **/
	private $bizDetail;
	
	/** 
	 * 更改积分数值
	 **/
	private $point;
	
	/** 
	 * minus:扣减;add:累加
	 **/
	private $type;
	
	/** 
	 * 用户昵称
	 **/
	private $userNick;
	
	private $apiParas = array();
	
	public function setBizCode($bizCode)
	{
		$this->bizCode = $bizCode;
		$this->apiParas["biz_code"] = $bizCode;
	}

	public function getBizCode()
	{
		return $this->bizCode;
	}

	public function setBizDetail($bizDetail)
	{
		$this->bizDetail = $bizDetail;
		$this->apiParas["biz_detail"] = $bizDetail;
	}

	public function getBizDetail()
	{
		return $this->bizDetail;
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

	public function setType($type)
	{
		$this->type = $type;
		$this->apiParas["type"] = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setUserNick($userNick)
	{
		$this->userNick = $userNick;
		$this->apiParas["user_nick"] = $userNick;
	}

	public function getUserNick()
	{
		return $this->userNick;
	}

	public function getApiMethodName()
	{
		return "tmall.crm.member.point.change";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->userNick,"userNick");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
