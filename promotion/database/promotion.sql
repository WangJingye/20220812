/*
SQLyog Ultimate v8.3 
MySQL - 5.5.5-10.1.25-MariaDB : Database - css_all_promotion
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`css_all_promotion` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */;

/*Table structure for table `gift` */

DROP TABLE IF EXISTS `gift`;

CREATE TABLE `gift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  `pic` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL,
  `qty` int(11) DEFAULT NULL COMMENT '库存',
  `used_qty` int(11) DEFAULT '0' COMMENT '使用库存',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '1:non-active;2:active',
  UNIQUE KEY `giftId` (`id`),
  KEY `status` (`status`),
  KEY `starttime` (`start_time`),
  KEY `endtime` (`end_time`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `promotion_cart` */

DROP TABLE IF EXISTS `promotion_cart`;

CREATE TABLE `promotion_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL COMMENT '显示名称',
  `type` varchar(255) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `priority` int(10) unsigned DEFAULT NULL COMMENT '弃用',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '1:未激活，2：激活,3:禁用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `exclude_sku` text COMMENT '条件-排除sku',
  `add_sku` text COMMENT '条件-添加sku',
  `cids` varchar(500) DEFAULT NULL COMMENT '材质分类',
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
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uniq_code` (`code_code`),
  KEY `status` (`status`),
  KEY `starttime` (`start_time`),
  KEY `endtime` (`end_time`),
  KEY `typeindex` (`type`(191)),
  KEY `total_discount_index` (`total_discount`(191))
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

/*Table structure for table `rule_type` */

DROP TABLE IF EXISTS `rule_type`;

CREATE TABLE `rule_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
