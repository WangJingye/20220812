<?php

/**
 * 网点/门店
 * @author auto create
 */
class ServiceStoreDTO
{
	
	/** 
	 * 城市
	 **/
	public $address_city;
	
	/** 
	 * 标准地址编码
	 **/
	public $address_code;
	
	/** 
	 * 详细地址
	 **/
	public $address_detail;
	
	/** 
	 * 地址-区
	 **/
	public $address_district;
	
	/** 
	 * 地址-省
	 **/
	public $address_province;
	
	/** 
	 * 标准地址-街道
	 **/
	public $address_town;
	
	/** 
	 * 支付宝账号
	 **/
	public $alipay_account;
	
	/** 
	 * 支付宝账户身份证号
	 **/
	public $alipay_account_id_number;
	
	/** 
	 * 支付宝账号姓名
	 **/
	public $alipay_account_name;
	
	/** 
	 * 额外属性。json
	 **/
	public $attributes;
	
	/** 
	 * 业务身份。汽车传car_accessory_install
	 **/
	public $biz_type;
	
	/** 
	 * 品牌认证资料url列表,英文逗号分隔
	 **/
	public $brand_certification;
	
	/** 
	 * 每日营业时间。字符串
	 **/
	public $business_hours;
	
	/** 
	 * 认证的天猫品牌id列表
	 **/
	public $certificated_brand_ids;
	
	/** 
	 * 公司名
	 **/
	public $company_name;
	
	/** 
	 * 门头照片
	 **/
	public $front_photo;
	
	/** 
	 * 纬度
	 **/
	public $latitude;
	
	/** 
	 * 法人身份证正面
	 **/
	public $legal_person_id_card_pic;
	
	/** 
	 * 法人身份证反面
	 **/
	public $legal_person_id_card_pic_back;
	
	/** 
	 * 法人身份证号
	 **/
	public $legal_person_id_number;
	
	/** 
	 * 法人姓名
	 **/
	public $legal_person_name;
	
	/** 
	 * 营业执照照片
	 **/
	public $license_photo;
	
	/** 
	 * 经度
	 **/
	public $longitude;
	
	/** 
	 * 经理人/联系人姓名
	 **/
	public $manager_name;
	
	/** 
	 * 经理人/联系人电话
	 **/
	public $manager_phone;
	
	/** 
	 * 对外服务座机号
	 **/
	public $phone;
	
	/** 
	 * 对外服务手机号
	 **/
	public $service_mobile;
	
	/** 
	 * 别名
	 **/
	public $service_store_alias;
	
	/** 
	 * 网点/门店编码
	 **/
	public $service_store_code;
	
	/** 
	 * 网点名称
	 **/
	public $service_store_name;
	
	/** 
	 * 统一社会信用代码
	 **/
	public $social_credit_code;
	
	/** 
	 * 门店类型
	 **/
	public $type;	
}
?>