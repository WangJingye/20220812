<?php
/**
 * TOP API: tmall.servicecenter.picture.upload request
 * 
 * @author auto create
 * @since 1.0, 2019.04.02
 */
class TmallServicecenterPictureUploadRequest
{
	/** 
	 * 图片文件二进制流
	 **/
	private $img;
	
	/** 
	 * true返回Https地址
	 **/
	private $isHttps;
	
	/** 
	 * 图片全称包括扩展名。目前支持 jpg jpeg png
	 **/
	private $pictureName;
	
	private $apiParas = array();
	
	public function setImg($img)
	{
		$this->img = $img;
		$this->apiParas["img"] = $img;
	}

	public function getImg()
	{
		return $this->img;
	}

	public function setIsHttps($isHttps)
	{
		$this->isHttps = $isHttps;
		$this->apiParas["is_https"] = $isHttps;
	}

	public function getIsHttps()
	{
		return $this->isHttps;
	}

	public function setPictureName($pictureName)
	{
		$this->pictureName = $pictureName;
		$this->apiParas["picture_name"] = $pictureName;
	}

	public function getPictureName()
	{
		return $this->pictureName;
	}

	public function getApiMethodName()
	{
		return "tmall.servicecenter.picture.upload";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->img,"img");
		RequestCheckUtil::checkNotNull($this->pictureName,"pictureName");
		RequestCheckUtil::checkMaxLength($this->pictureName,32,"pictureName");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
