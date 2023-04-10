/*
SQLyog Ultimate v12.08 (64 bit)
MySQL - 5.7.26 : Database - el_member
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`el_member` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */;

USE `el_member`;

/*Table structure for table `browse_history` */

DROP TABLE IF EXISTS `browse_history`;

CREATE TABLE `browse_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `wechat_user_id` varchar(50) DEFAULT NULL COMMENT '用户id',
  `product_id` varchar(255) DEFAULT NULL COMMENT '产品id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `wechat_user_id` (`wechat_user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `browse_history` */

/*Table structure for table `crm_auth_token` */

DROP TABLE IF EXISTS `crm_auth_token`;

CREATE TABLE `crm_auth_token` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `token` varchar(1000) DEFAULT NULL COMMENT 'token值',
  `status` tinyint(1) DEFAULT NULL COMMENT '0过期 1可用',
  `expired_at` int(10) DEFAULT NULL COMMENT '过期时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `crm_auth_token` */

/*Table structure for table `crm_customers` */

DROP TABLE IF EXISTS `crm_customers`;

CREATE TABLE `crm_customers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `wechat_user_id` int(11) NOT NULL COMMENT '关联wechat_id',
  `customer_id` varchar(50) NOT NULL DEFAULT '' COMMENT 'CustomerID',
  `family_name` varchar(50) NOT NULL DEFAULT '''''' COMMENT '姓',
  `first_name` varchar(100) NOT NULL DEFAULT '''''' COMMENT '名',
  `gender` varchar(11) NOT NULL DEFAULT 'M' COMMENT '性别 M男 F女',
  `salute` varchar(4) DEFAULT '01' COMMENT '称谓 01先生 02小姐 03女士 04太太',
  `date_of_birth` varchar(50) DEFAULT NULL COMMENT '生日',
  `email` varchar(50) DEFAULT NULL COMMENT 'E-mail',
  `residence_country` varchar(100) NOT NULL DEFAULT '''''' COMMENT '居住地',
  `mobile_country_code` varchar(10) NOT NULL DEFAULT '''''' COMMENT '地区代码',
  `mobile_number` varchar(25) NOT NULL DEFAULT '' COMMENT '手机号码',
  `member_class` varchar(4) DEFAULT 'FS' COMMENT '等级',
  `stateCode` varchar(4) DEFAULT 'P' COMMENT 'P:待验证的周友账号 V:有效的周友账号',
  `available` tinyint(4) DEFAULT '1' COMMENT '1激活 2 冻结',
  `fromchannel` tinyint(4) DEFAULT '1' COMMENT '1小程序周友 2老周友 3分店导购入会',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_wechat_user_id` (`wechat_user_id`) USING BTREE,
  KEY `idx_fromchannel` (`fromchannel`) USING BTREE,
  KEY `member_class` (`member_class`) USING BTREE,
  KEY `mobile_number` (`mobile_number`) USING BTREE,
  KEY `email` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `crm_customers` */

/*Table structure for table `employee` */

DROP TABLE IF EXISTS `employee`;

CREATE TABLE `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `openid` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '微信openid',
  `wechat_id` int(11) DEFAULT NULL COMMENT 'wechat_id',
  `user_id` int(11) NOT NULL COMMENT 'user_id',
  `empid` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购主键id ',
  `guide_code` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购编码',
  `store_code` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购门店id',
  `spuid` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '产品 唯一吗',
  `skuid` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'sku 唯一吗',
  `ordered_flag` tinyint(4) DEFAULT '0' COMMENT '0 未下单  1  已下单  2  失效',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `order_goods_id` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '订单行编号',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  UNIQUE KEY `id` (`id`),
  KEY `openid_idx` (`openid`(191)),
  KEY `empid_idx` (`empid`(191)),
  KEY `spuid_idx` (`spuid`),
  KEY `skuid_idx` (`skuid`),
  KEY `flag_idx` (`ordered_flag`),
  KEY `wechat_id_idx` (`wechat_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Data for the table `employee` */

/*Table structure for table `employee_code` */

DROP TABLE IF EXISTS `employee_code`;

CREATE TABLE `employee_code` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `data` text COLLATE utf8mb4_bin COMMENT '详细数据',
  `guide_code` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购id',
  `store_code` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购门店id',
  `sku_id` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'sku_id',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='导购分享太阳码记录表';

/*Data for the table `employee_code` */

/*Table structure for table `employee_member` */

DROP TABLE IF EXISTS `employee_member`;

CREATE TABLE `employee_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `openid` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '微信openid',
  `wechat_id` int(11) DEFAULT NULL COMMENT 'wechat_id',
  `user_id` int(11) DEFAULT NULL COMMENT 'user_id',
  `sku_id` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'sku_id',
  `empid` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购主键id ',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  UNIQUE KEY `id` (`id`),
  KEY `empid_idx` (`empid`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Data for the table `employee_member` */

/*Table structure for table `employee_user_share` */

DROP TABLE IF EXISTS `employee_user_share`;

CREATE TABLE `employee_user_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `openid` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '微信openid',
  `wechat_id` int(11) DEFAULT NULL COMMENT 'wechat_id',
  `user_id` int(11) NOT NULL COMMENT 'user_id',
  `empid` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购主键id ',
  `guide_code` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购编码',
  `store_code` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购门店id',
  `spuid` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '产品 唯一吗',
  `skuid` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'sku 唯一吗',
  `ordered_flag` tinyint(4) DEFAULT '0' COMMENT '0 未下单  1  已下单  2  失效',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `order_goods_id` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '订单行编号',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  UNIQUE KEY `id` (`id`),
  KEY `openid_idx` (`openid`(191)),
  KEY `empid_idx` (`empid`(191)),
  KEY `spuid_idx` (`spuid`),
  KEY `skuid_idx` (`skuid`),
  KEY `flag_idx` (`ordered_flag`),
  KEY `wechat_id_idx` (`wechat_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='普通会员分享';

/*Data for the table `employee_user_share` */

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `failed_jobs` */

/*Table structure for table `favorite_records` */

DROP TABLE IF EXISTS `favorite_records`;

CREATE TABLE `favorite_records` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `wechat_user_id` int(11) NOT NULL COMMENT '用户ID',
  `product_id` varchar(255) NOT NULL DEFAULT '' COMMENT '产品id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `wechat_user_id` (`wechat_user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `favorite_records` */

/*Table structure for table `fission` */

DROP TABLE IF EXISTS `fission`;

CREATE TABLE `fission` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '裂变名称',
  `start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `status` tinyint(1) DEFAULT NULL COMMENT '1:未激活，2：激活,3:禁用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `condition_value` text COMMENT '条件值',
  `max_num` int(10) DEFAULT NULL COMMENT '赠送上线',
  `step` tinyint(1) DEFAULT '0' COMMENT '是否多增   2   多赠   1 赠1个',
  `value_id` text COMMENT '优惠券/优惠码id',
  `type` tinyint(1) DEFAULT NULL COMMENT '类型   1   优惠码  2   优惠卷',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `fission` */

/*Table structure for table `fission_award_log` */

DROP TABLE IF EXISTS `fission_award_log`;

CREATE TABLE `fission_award_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fission_id` int(10) NOT NULL COMMENT '裂变id',
  `code_id` int(10) DEFAULT NULL COMMENT '优惠券、优惠码id',
  `type` tinyint(1) DEFAULT NULL COMMENT '类型   1   优惠码  2   优惠卷',
  `member_name` varchar(255) DEFAULT NULL COMMENT '用户昵称',
  `member_id` int(10) DEFAULT NULL COMMENT '用户id',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `mid` (`member_id`),
  KEY `fission_id` (`fission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `fission_award_log` */

/*Table structure for table `gold_price_consultation` */

DROP TABLE IF EXISTS `gold_price_consultation`;

CREATE TABLE `gold_price_consultation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(100) DEFAULT '' COMMENT '名字',
  `eng_name` varchar(100) DEFAULT NULL COMMENT '英文名字',
  `type` varchar(50) DEFAULT NULL COMMENT '类型',
  `price` int(11) DEFAULT NULL COMMENT '金价，单元（分）',
  `entry_date` varchar(50) DEFAULT NULL COMMENT '金价时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `gold_price_consultation` */

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `jobs_queue_index` (`queue`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `jobs` */

/*Table structure for table `liveplayer` */

DROP TABLE IF EXISTS `liveplayer`;

CREATE TABLE `liveplayer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `roomid` varchar(50) DEFAULT NULL,
  `cover_img` varchar(255) DEFAULT NULL,
  `share_img` varchar(255) DEFAULT NULL,
  `live_status` int(10) unsigned DEFAULT NULL,
  `start_time` int(10) unsigned DEFAULT NULL,
  `end_time` int(10) unsigned DEFAULT NULL,
  `anchor_name` varchar(255) DEFAULT NULL,
  `goods` text,
  `liveplayer_ing_img` varchar(255) DEFAULT NULL,
  `liveplayer_list_img` varchar(255) DEFAULT NULL,
  `liveplayer_replay_img` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `liveplayer` */

/*Table structure for table `member_merge_record` */

DROP TABLE IF EXISTS `member_merge_record`;

CREATE TABLE `member_merge_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `old_member_code` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '旧会员卡号',
  `new_member_code` varchar(100) COLLATE utf8mb4_bin NOT NULL COMMENT '新会员卡号',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `key` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '是否合并完成 1:完成,2:异常',
  `msg` text COLLATE utf8mb4_bin COMMENT '合并信息',
  `file_path` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '合并的xml文件本地路径',
  `ori_uid` int(10) DEFAULT NULL COMMENT '原始商城用户ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_u` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Data for the table `member_merge_record` */

/*Table structure for table `member_relation` */

DROP TABLE IF EXISTS `member_relation`;

CREATE TABLE `member_relation` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pid` int(10) NOT NULL COMMENT '裂变用户父类id',
  `sub_id` int(10) NOT NULL COMMENT '子类id',
  `fission_id` int(10) NOT NULL COMMENT '裂变表主键id',
  `pic` varchar(255) DEFAULT NULL COMMENT '头像',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态 脚本过滤  0 未过滤  1   已过滤',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `subid` (`sub_id`),
  KEY `fission_id` (`fission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `member_relation` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `migrations` */

/*Table structure for table `sa_list` */

DROP TABLE IF EXISTS `sa_list`;

CREATE TABLE `sa_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sid` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `headimgurl` varchar(255) DEFAULT NULL,
  `role_id` varchar(10) DEFAULT NULL COMMENT '角色id',
  `role_name` varchar(20) DEFAULT NULL COMMENT '角色',
  `store_id` int(11) DEFAULT NULL COMMENT '门店id',
  `store_name` varchar(100) DEFAULT NULL COMMENT '门店名称',
  `address` varchar(200) DEFAULT NULL COMMENT '地址',
  `is_bind` tinyint(1) DEFAULT '0' COMMENT '是否绑定过用户',
  `status` tinyint(4) DEFAULT '1' COMMENT '员工状态 1在职 2离职 3冻结 4休假',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `sid` (`sid`) USING BTREE,
  UNIQUE KEY `phone` (`phone`),
  KEY `name` (`name`),
  KEY `role_name` (`role_name`),
  KEY `store_name` (`store_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='导购';

/*Data for the table `sa_list` */

/*Table structure for table `share_event` */

DROP TABLE IF EXISTS `share_event`;

CREATE TABLE `share_event` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '活动ID',
  `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT '活动名称',
  `start_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  `end_time` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型,1:拉新,2:下单',
  `status` tinyint(1) DEFAULT '0' COMMENT '开关',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `background_image` varchar(150) COLLATE utf8_bin DEFAULT NULL COMMENT '裂变背景大图',
  `share_image` varchar(150) COLLATE utf8_bin DEFAULT NULL COMMENT '分享海报图',
  `share_icon` varchar(150) COLLATE utf8_bin DEFAULT NULL COMMENT '分享图标',
  `rules` text COLLATE utf8_bin COMMENT '活动规则文案',
  `show_num` tinyint(2) DEFAULT '1' COMMENT '邀请显示人数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `share_event` */

/*Table structure for table `share_num` */

DROP TABLE IF EXISTS `share_num`;

CREATE TABLE `share_num` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `event_id` int(10) NOT NULL COMMENT '活动ID',
  `sharer_id` varchar(100) COLLATE utf8_bin NOT NULL COMMENT '分享者ID',
  `num` int(10) DEFAULT '1' COMMENT '成功分享人数',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `share_event_u` (`event_id`,`sharer_id`),
  KEY `sharer_id_i` (`sharer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `share_num` */

/*Table structure for table `share_relation` */

DROP TABLE IF EXISTS `share_relation`;

CREATE TABLE `share_relation` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `event_id` int(10) NOT NULL COMMENT '活动ID',
  `sharer_id` varchar(100) COLLATE utf8_bin NOT NULL COMMENT '分享者用户ID',
  `friend_id` varchar(100) COLLATE utf8_bin NOT NULL COMMENT '被分享者用户ID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '0' COMMENT '是否有效',
  `order_id` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT '分享下单订单号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `esf_u` (`event_id`,`sharer_id`,`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `share_relation` */

/*Table structure for table `tb_active_users` */

DROP TABLE IF EXISTS `tb_active_users`;

CREATE TABLE `tb_active_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `channel` varchar(255) DEFAULT NULL COMMENT '渠道',
  `active` varchar(255) DEFAULT NULL COMMENT '活动',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户活动参与表';

/*Data for the table `tb_active_users` */

/*Table structure for table `tb_employee` */

DROP TABLE IF EXISTS `tb_employee`;

CREATE TABLE `tb_employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '员工ID',
  `store_id` int(11) DEFAULT NULL,
  `is_deleted` int(2) DEFAULT '0' COMMENT '离职状态 （0 在职 1 离职)',
  `region` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '所属地区',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '电话',
  `name_en` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '英文名',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '员工姓名',
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '邮箱',
  `role_id` int(11) DEFAULT NULL COMMENT '职位id',
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '职务',
  `address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '地址',
  `hasSaleRole` int(1) DEFAULT '0' COMMENT '是否有分店员工角色(0 无 1有)',
  `hasSaleManagerRole` int(1) DEFAULT NULL COMMENT '是否有分店经理角色(0 无 1有)',
  `hasAreaManagerRole` int(1) DEFAULT NULL COMMENT '是否有营运经理角色(0 无 1有)',
  `dimission_at` datetime DEFAULT NULL COMMENT '离职日期',
  `probation_at` datetime DEFAULT NULL COMMENT '试用期日期',
  `store_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '店铺名称',
  `created_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '新增人',
  `updated_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '最后编辑人',
  `created_at` datetime DEFAULT NULL COMMENT '新增日期',
  `updated_at` datetime DEFAULT NULL COMMENT '最后编辑日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_employee` */

/*Table structure for table `tb_favorite` */

DROP TABLE IF EXISTS `tb_favorite`;

CREATE TABLE `tb_favorite` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `product_idx` int(11) unsigned NOT NULL COMMENT '商品自增ID，对应sisley_goods.tb_product.id',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `type` tinyint(1) DEFAULT '1' COMMENT '1商品 2商品集合',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `user_id` (`user_id`,`product_idx`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_favorite` */

/*Table structure for table `tb_footprint` */

DROP TABLE IF EXISTS `tb_footprint`;

CREATE TABLE `tb_footprint` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `product_idx` int(11) unsigned NOT NULL COMMENT '商品自增ID，对应sisley_goods.tb_product.id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `type` tinyint(1) DEFAULT '1' COMMENT '1商品 2商品集合',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `user_id` (`user_id`,`product_idx`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_footprint` */

/*Table structure for table `tb_points_log` */

DROP TABLE IF EXISTS `tb_points_log`;

CREATE TABLE `tb_points_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pos_id` varchar(20) COLLATE utf8mb4_bin NOT NULL,
  `type` tinyint(1) DEFAULT '1' COMMENT '1用户下单2下单取消',
  `point` int(10) DEFAULT NULL COMMENT '积分 正数加负数减',
  `remarks` varchar(255) COLLATE utf8mb4_bin DEFAULT '' COMMENT '描述',
  `source` varchar(20) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '渠道',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `pos_id_index` (`pos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Data for the table `tb_points_log` */

/*Table structure for table `tb_social_relations` */

DROP TABLE IF EXISTS `tb_social_relations`;

CREATE TABLE `tb_social_relations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '用户ID，对应users表id，索引',
  `open_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '三方登录用户唯一标示，索引',
  `union_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '微信专属，索引',
  `social_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '社交类型 wechat weibo qq miniapp索引 ',
  `social_info` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_union_id` (`union_id`) USING BTREE,
  UNIQUE KEY `users_open_id` (`open_id`) USING BTREE,
  KEY `users_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_social_relations` */

/*Table structure for table `tb_store` */

DROP TABLE IF EXISTS `tb_store`;

CREATE TABLE `tb_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) DEFAULT NULL COMMENT '店铺id',
  `store_code` int(10) DEFAULT '0' COMMENT '店铺编号',
  `lat` decimal(10,5) DEFAULT '0.00000' COMMENT '纬度',
  `lon` decimal(10,5) DEFAULT '0.00000' COMMENT '经度',
  `mobile` varchar(11) COLLATE utf8mb4_bin DEFAULT '' COMMENT '手机号',
  `tel` varchar(20) COLLATE utf8mb4_bin DEFAULT '' COMMENT '联系电话',
  `store_name` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT '店铺名称',
  `city` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT '市',
  `province` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT '省',
  `area` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT '区',
  `address` varchar(255) COLLATE utf8mb4_bin DEFAULT '' COMMENT '地址',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否更新过坐标    0   未更新 1  已更新',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='店铺';

/*Data for the table `tb_store` */

/*Table structure for table `tb_store_role` */

DROP TABLE IF EXISTS `tb_store_role`;

CREATE TABLE `tb_store_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_english_name` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `role_name` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '职位名称',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='店铺角色名称';

/*Data for the table `tb_store_role` */

/*Table structure for table `tb_user_address` */

DROP TABLE IF EXISTS `tb_user_address`;

CREATE TABLE `tb_user_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `sex` tinyint(1) DEFAULT '1' COMMENT '性别 1女士 2先生',
  `name` varchar(100) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '姓名',
  `zip_code` varchar(30) COLLATE utf8mb4_bin DEFAULT '0' COMMENT '邮政编号',
  `mobile` varchar(20) COLLATE utf8mb4_bin DEFAULT '' COMMENT '手机号',
  `city` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT '市',
  `province` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT '省',
  `area` varchar(50) COLLATE utf8mb4_bin DEFAULT '' COMMENT '区',
  `address` varchar(255) COLLATE utf8mb4_bin DEFAULT '' COMMENT '地址',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '是否为默认地址 1是 0否',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='用户地址簿';

/*Data for the table `tb_user_address` */

/*Table structure for table `tb_user_coupon` */

DROP TABLE IF EXISTS `tb_user_coupon`;

CREATE TABLE `tb_user_coupon` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `coupon_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `type` tinyint(1) unsigned DEFAULT '2' COMMENT '优惠券类型 1新客 2普通',
  `received_at` timestamp NULL DEFAULT NULL COMMENT '领取时间',
  `used_at` timestamp NULL DEFAULT NULL COMMENT '使用时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `revert_at` timestamp NULL DEFAULT NULL COMMENT '归还时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`,`coupon_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_user_coupon` */

/*Table structure for table `tb_users` */

DROP TABLE IF EXISTS `tb_users`;

CREATE TABLE `tb_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sex` tinyint(1) DEFAULT '0' COMMENT '1 男 2 女',
  `source_type` tinyint(1) DEFAULT '0' COMMENT '0老官网 1sisley上线后会员 2其他 ',
  `level` tinyint(1) DEFAULT '0' COMMENT '0 新客 ，1 普卡， 2金卡 ，3白金卡，4老客，5贵宾',
  `guid_id` int(10) DEFAULT NULL COMMENT '导购id',
  `parent_id` int(10) DEFAULT NULL COMMENT '推荐人',
  `channel` int(10) DEFAULT NULL COMMENT '渠道',
  `points` int(10) DEFAULT '0' COMMENT '积分',
  `respect_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '尊称：先生 小姐 女士 太太',
  `birth` date DEFAULT NULL COMMENT '生日，格式2001-01-01\r\n生日，格式2001-01-01\r\n生日，格式2001-01-01',
  `guid_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '导购姓名',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '手机号',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户名',
  `nickname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `last_name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名',
  `status` tinyint(1) DEFAULT '1' COMMENT '0:已删除,1:正常,2:暂停',
  `first_name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '姓',
  `open_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '小程序openid',
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '邮箱',
  `pos_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CRM会员ID',
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户session id，生成之后不会变化',
  `password` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '密码',
  `province` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `pic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `from_activity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '来源活动',
  `from_entrance` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '活动入口',
  `city` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `country` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `telephone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `age_group` int(11) DEFAULT '0',
  `pc_login_at` timestamp NULL DEFAULT NULL COMMENT '电脑端登录时间',
  `guide_id` int(11) NOT NULL DEFAULT '0',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mini_login_at` timestamp NULL DEFAULT NULL COMMENT '小程序登录时间',
  `mobile_login_at` timestamp NULL DEFAULT NULL COMMENT '手机端登录时间',
  `share_from` int(10) DEFAULT '0' COMMENT '来自分享人',
  `union_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '小程序unionid',
  `avatar_url` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '小程序头像',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_phone_unique` (`phone`),
  UNIQUE KEY `openid_u` (`open_id`),
  KEY `users_name_unique` (`name`) USING BTREE,
  KEY `users_email_unique` (`email`) USING BTREE,
  KEY `user_parent_id` (`parent_id`),
  KEY `user_guid_id` (`guid_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1058293 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_users` */

insert  into `tb_users`(`id`,`sex`,`source_type`,`level`,`guid_id`,`parent_id`,`channel`,`points`,`respect_type`,`birth`,`guid_name`,`phone`,`name`,`nickname`,`last_name`,`status`,`first_name`,`open_id`,`email`,`pos_id`,`session_id`,`password`,`province`,`pic`,`from_activity`,`from_entrance`,`city`,`country`,`telephone`,`age_group`,`pc_login_at`,`guide_id`,`updated_at`,`created_at`,`mini_login_at`,`mobile_login_at`,`share_from`,`union_id`,`avatar_url`) values (1058230,0,2,0,NULL,NULL,3,0,'','1979-07-06',NULL,'17701761566','史蒂汶','','',1,'','omBzf4qwFLjOOl7tYsXuis_0-uok',NULL,'2998000070',NULL,'fd4c1d36097313fe4a65d1979fe77101:11','','',NULL,NULL,'','','',0,'2020-11-26 15:29:26',0,'2021-05-28 16:15:17','2020-08-12 10:49:45','2020-11-25 13:41:34','2020-09-22 17:16:00',0,'','https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJxoV3C6H94w0MYgZEYhPsCVdUbKN96hsQT4Pic9ETuScXNU45Bibu6whD23gt8otA0thECF3YrD3Bw/132');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_email_unique` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `users` */

/*Table structure for table `wbs` */

DROP TABLE IF EXISTS `wbs`;

CREATE TABLE `wbs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) DEFAULT '0' COMMENT '状态',
  `coupon_id` int(11) DEFAULT NULL COMMENT '优惠劵id',
  `exchange_point` int(11) DEFAULT NULL COMMENT '兑换积分',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wbs_coupon_id_uindex` (`coupon_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='积分商城';

/*Data for the table `wbs` */

/*Table structure for table `wbs_user_point` */

DROP TABLE IF EXISTS `wbs_user_point`;

CREATE TABLE `wbs_user_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_coupon_id` int(11) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `wbs_id` int(11) DEFAULT NULL,
  `coupon_id` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `coupon_type` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `order_id` varchar(50) COLLATE utf8mb4_bin DEFAULT '',
  `marker` text COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `tb_user_point_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='积分商城';

/*Data for the table `wbs_user_point` */

/*Table structure for table `wechat_coupons` */

DROP TABLE IF EXISTS `wechat_coupons`;

CREATE TABLE `wechat_coupons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `wechat_user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `coupon_id` int(11) DEFAULT NULL COMMENT '优惠券id',
  `type` tinyint(1) unsigned DEFAULT '2' COMMENT '优惠券类型 1新客 2普通',
  `received_at` timestamp NULL DEFAULT NULL COMMENT '领取时间',
  `used_at` timestamp NULL DEFAULT NULL COMMENT '使用时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `wechat_user_id` (`wechat_user_id`,`coupon_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `wechat_coupons` */

/*Table structure for table `wechat_users` */

DROP TABLE IF EXISTS `wechat_users`;

CREATE TABLE `wechat_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `unionid` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户unionid',
  `openid` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '用户openid',
  `session_key` varchar(100) CHARACTER SET utf8 DEFAULT '''''' COMMENT '会话Key',
  `nickName` varchar(1000) CHARACTER SET utf8 DEFAULT NULL COMMENT '微信昵称',
  `avatarUrl` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '头像',
  `gender` tinyint(5) DEFAULT '0' COMMENT '性别 0未知 1男 2女',
  `phoneCode` varchar(11) CHARACTER SET utf8 DEFAULT NULL COMMENT '区号',
  `phone` varchar(11) CHARACTER SET utf8 DEFAULT NULL COMMENT '授权手机号',
  `country` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '国家',
  `province` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '城市',
  `authorize_at` timestamp NULL DEFAULT NULL COMMENT '授权时间',
  `isNew` tinyint(1) DEFAULT '1' COMMENT '新客 1是，还没领取过新客优惠券 0否',
  `guide_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '导购 默认为0不是  ',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `openid` (`openid`) USING BTREE,
  KEY `created_at` (`created_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `wechat_users` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
