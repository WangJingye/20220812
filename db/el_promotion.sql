/*
SQLyog Ultimate v12.08 (64 bit)
MySQL - 5.7.26 : Database - el_promotion
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`el_promotion` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */;

USE `el_promotion`;

/*Table structure for table `gift` */

DROP TABLE IF EXISTS `gift`;

CREATE TABLE `gift` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '名字',
  `pic` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '图片',
  `qty` int(11) DEFAULT NULL COMMENT '库存',
  `used_qty` int(11) DEFAULT '0' COMMENT '使用库存',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `status` tinyint(4) DEFAULT '1' COMMENT '1:non-active;2:active',
  UNIQUE KEY `giftId` (`id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `starttime` (`start_time`) USING BTREE,
  KEY `endtime` (`end_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='赠品';

/*Data for the table `gift` */

/*Table structure for table `log` */

DROP TABLE IF EXISTS `log`;

CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `userId` int(11) NOT NULL COMMENT '操作人员id',
  `userEmail` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '操作人员email',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `actionType` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '操作类型',
  `ruleId` int(11) DEFAULT NULL COMMENT '促销规则id',
  `giftId` int(11) DEFAULT NULL COMMENT '赠品id',
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `ruleid_idx` (`ruleId`) USING BTREE,
  KEY `giftid_idx` (`giftId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC COMMENT='促销后台操作日记';

/*Data for the table `log` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `migrations` */

/*Table structure for table `promotion_cart` */

DROP TABLE IF EXISTS `promotion_cart`;

CREATE TABLE `promotion_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(255) DEFAULT NULL COMMENT '促销名称',
  `display_name` varchar(255) DEFAULT NULL COMMENT '前台显示名称',
  `type` varchar(255) NOT NULL COMMENT '促销类型',
  `start_time` datetime DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '结束时间',
  `priority` int(10) unsigned DEFAULT NULL COMMENT '优先级',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '1:未激活，2：激活,3:禁用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `exclude_sku` text COMMENT '条件-排除sku',
  `add_sku` text COMMENT '条件-添加sku',
  `cids` text COMMENT '材质分类',
  `addrules` text COMMENT '叠加规则',
  `total_amount` varchar(200) DEFAULT NULL COMMENT '满减-满',
  `total_discount` varchar(200) DEFAULT NULL COMMENT '满减-减',
  `product_discount` float DEFAULT NULL COMMENT '直接折扣',
  `nn_n` varchar(200) DEFAULT NULL COMMENT 'n件n折-件',
  `nn_discount` varchar(200) DEFAULT NULL COMMENT 'n件n折-折',
  `step_amount` varchar(200) DEFAULT NULL COMMENT '每满减-满',
  `step_discount` varchar(200) DEFAULT NULL COMMENT '每满减-减',
  `gift_id` int(11) DEFAULT NULL COMMENT '赠品id',
  `gift_amount` float DEFAULT NULL COMMENT '赠品-满',
  `gift_n` int(11) DEFAULT NULL COMMENT '赠品-件',
  `coupon_stock` int(11) DEFAULT NULL COMMENT '优惠券库存',
  `coupon_stock_used` int(11) DEFAULT '0' COMMENT '优惠券领取数量',
  `coupon_description` varchar(500) DEFAULT NULL COMMENT '优惠券描述',
  `code_length` int(11) DEFAULT NULL COMMENT '优惠码长度',
  `code_stock` int(11) DEFAULT NULL COMMENT '优惠码库存',
  `code_stock_used` int(11) DEFAULT '0' COMMENT '优惠码使用次数',
  `code_code` varchar(50) DEFAULT NULL COMMENT '优惠码-码',
  `used_times` int(11) DEFAULT '0' COMMENT '使用次数',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `member_use_count` int(11) NOT NULL DEFAULT '1',
  `show_page` varchar(50) DEFAULT NULL,
  `gwp_skus` varchar(200) DEFAULT NULL COMMENT '赠品的skus,用逗号分隔',
  `ship_fee` int(11) DEFAULT '0' COMMENT '付邮试用的运费',
  `coupon_type` tinyint(4) DEFAULT '1' COMMENT '优惠券类型,1普通优惠券,2实物券',
  `product_coupon_sku` varchar(200) DEFAULT NULL COMMENT '实物券sku',
  `expire_days` int(11) DEFAULT '0' COMMENT '领取后过期时间',
  `product_coupon_pic` varchar(500) DEFAULT NULL COMMENT '实物券图片',
  `product_coupon_name` varchar(200) DEFAULT NULL COMMENT '实物券商品名称',
  `is_step` tinyint(1) DEFAULT '0' COMMENT '是否选择步长',
  `is_whole` tinyint(1) DEFAULT '0' COMMENT '是否全场',
  `is_rand` tinyint(1) DEFAULT '0' COMMENT '是否是赠品',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `starttime` (`start_time`) USING BTREE,
  KEY `endtime` (`end_time`) USING BTREE,
  KEY `typeindex` (`type`(191)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='促销规则';

/*Data for the table `promotion_cart` */

/*Table structure for table `rule_type` */

DROP TABLE IF EXISTS `rule_type`;

CREATE TABLE `rule_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `key` varchar(255) DEFAULT NULL COMMENT '促销标识',
  `label` varchar(255) DEFAULT NULL COMMENT '促销名字',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='促销类型';

/*Data for the table `rule_type` */

/*Table structure for table `services_code` */

DROP TABLE IF EXISTS `services_code`;

CREATE TABLE `services_code` (
  `service_id` int(11) DEFAULT NULL,
  `service_code` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `services_code_service_id_index` (`service_id`),
  CONSTRAINT `services_code_wbs_id_fk` FOREIGN KEY (`service_id`) REFERENCES `wbs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Data for the table `services_code` */

/*Table structure for table `ship_fee` */

DROP TABLE IF EXISTS `ship_fee`;

CREATE TABLE `ship_fee` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `province` varchar(30) COLLATE utf8mb4_bin NOT NULL COMMENT '省份',
  `ship_fee` decimal(4,4) DEFAULT '0.0000' COMMENT '运费',
  `free_limit` int(10) DEFAULT '0' COMMENT '满X免运费',
  `is_free` tinyint(1) DEFAULT NULL COMMENT '是否免邮',
  PRIMARY KEY (`id`),
  UNIQUE KEY `province_u` (`province`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Data for the table `ship_fee` */

/*Table structure for table `wbs` */

DROP TABLE IF EXISTS `wbs`;

CREATE TABLE `wbs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'product,coupon,services',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态',
  `product_sku` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '商品sku',
  `coupon_id` int(11) DEFAULT NULL COMMENT '优惠劵id',
  `service_name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '服务名称\n',
  `exchange_point` int(11) DEFAULT NULL COMMENT '兑换积分',
  `qty` int(11) DEFAULT NULL COMMENT '库存',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='积分商城';

/*Data for the table `wbs` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
