/*
SQLyog Ultimate v12.08 (64 bit)
MySQL - 5.7.26 : Database - el_orders
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`el_orders` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */;

USE `el_orders`;

/*Table structure for table `cart` */

DROP TABLE IF EXISTS `cart`;

CREATE TABLE `cart` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `wechat_id` int(11) NOT NULL COMMENT '顾客wechat id',
  `origin` tinyint(1) DEFAULT '0' COMMENT '来源   0  购物车   1     立即购买',
  `code_id` char(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '优惠码',
  `point_id` tinyint(1) DEFAULT NULL COMMENT '使用悦享钱应用方式  ',
  `coupon_id` int(11) DEFAULT NULL COMMENT '优惠卷id',
  `pdt_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品原价',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '付款金额',
  `total_discount` decimal(10,2) DEFAULT '0.00' COMMENT '优惠总金额',
  `total_product_price` decimal(10,2) DEFAULT NULL COMMENT '商品原总金额',
  `total_product_discount` decimal(10,2) DEFAULT '0.00' COMMENT '商品折扣金额',
  `total_order_discount` decimal(10,2) DEFAULT '0.00' COMMENT '满减折扣',
  `total_point_discount` decimal(10,2) DEFAULT '0.00' COMMENT '悦享钱优惠金额',
  `total_member_discount` decimal(10,2) DEFAULT '0.00' COMMENT '会员优惠金额',
  `total_code_discount` decimal(10,2) DEFAULT '0.00' COMMENT '优惠码优惠金额',
  `total_coupon_discount` decimal(10,2) DEFAULT '0.00' COMMENT '红包优惠金额',
  `total_amount` decimal(10,2) DEFAULT '0.00' COMMENT '付款金额',
  `give_point` int(10) DEFAULT '0' COMMENT '获得悦享钱',
  `total_points` decimal(10,2) DEFAULT '0.00' COMMENT '使用悦享钱数量',
  `combile` tinyint(1) DEFAULT '0' COMMENT '是否更新促销规则',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `wechat_id` (`wechat_id`) USING BTREE,
  KEY `origin` (`origin`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='购物车表';

/*Data for the table `cart` */

/*Table structure for table `cart_items` */

DROP TABLE IF EXISTS `cart_items`;

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '购物车ID',
  `cart_id` int(11) DEFAULT NULL COMMENT '购物车id',
  `customer_id` char(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '周友账户id',
  `spu_id` char(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'spu',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商品名称',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '图片地址',
  `sku_id` char(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'sku',
  `style_number` char(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '款号 ',
  `model_number` char(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'model number',
  `metarial` char(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '材质',
  `product_type` char(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '类别',
  `price_type` char(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '价格类型   定价  计价 Y',
  `inventory` int(11) DEFAULT '1' COMMENT '商品数',
  `gold_price` decimal(10,2) DEFAULT '0.00' COMMENT '当前金价',
  `weight` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '重量',
  `origin_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品原价',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '商品价格',
  `final_price` decimal(10,2) DEFAULT NULL COMMENT '最终价格',
  `labor_price` decimal(10,2) DEFAULT '0.00' COMMENT '工费',
  `promotion_type` char(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'pro 折扣   member 会员 point 悦享钱  auto ',
  `promotion_money` decimal(10,2) DEFAULT '0.00' COMMENT '促销优惠金额',
  `promotion_sale` decimal(10,2) DEFAULT '0.00' COMMENT '已享受的，促销，满减，优惠金额',
  `promotion_member` decimal(10,2) DEFAULT '0.00' COMMENT '会员优惠',
  `promotion_discount` decimal(10,2) DEFAULT '0.00' COMMENT '已享受的，促销，打折，优惠金额',
  `promotion_code` decimal(10,2) DEFAULT '0.00' COMMENT '优惠码优惠金额',
  `promotion_point` decimal(10,2) DEFAULT '0.00' COMMENT '悦享钱 对应优惠金额',
  `give_point` int(10) DEFAULT '0' COMMENT '获得悦享钱数量',
  `point` int(10) DEFAULT '0' COMMENT '使用悦享钱数量',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '优惠金额',
  `applied_coupon` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '是否应用红包',
  `applied_cut` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '是否应用满减',
  `applied_member` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '是否应用会员折扣',
  `applied_discount` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '是否应用折扣',
  `applied_rule_ids` text COLLATE utf8mb4_unicode_ci COMMENT '应用的优惠集合',
  `applied_gift` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '是否应用赠品',
  `applied_point` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '是否可用悦享钱',
  `lottery_font` tinyint(1) DEFAULT '1' COMMENT '字体，1roma2是arial',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '刻字内容',
  `max_length` tinyint(3) DEFAULT NULL COMMENT '刻字长度限制',
  `next_tips` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '凑单显示',
  `is_able` int(1) DEFAULT '1' COMMENT ' 1  正常   2   当前sku已售罄  3   spu 产品全售罄',
  `is_gift` tinyint(1) DEFAULT '0' COMMENT '是否为赠品   1   是    0   不是',
  `is_charme` tinyint(1) DEFAULT '0' COMMENT '0   普通产品   1    手绳（不需要提交oms锁库存）',
  `is_service` tinyint(1) DEFAULT '0' COMMENT '是否可退换货   0  否  1  是',
  `is_lettory` tinyint(1) DEFAULT NULL COMMENT '是否可刻字',
  `presale_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '预售时间',
  `pid` int(10) DEFAULT NULL COMMENT 'sku_id',
  `guide` char(20) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '导购id',
  `channel` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '渠道id',
  `store_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'guide 对应门店id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `is_able` (`is_able`) USING BTREE,
  KEY `cart_id` (`cart_id`) USING BTREE,
  KEY `custid` (`customer_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

/*Data for the table `cart_items` */

/*Table structure for table `cart_redis` */

DROP TABLE IF EXISTS `cart_redis`;

CREATE TABLE `cart_redis` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `redis_key` varchar(100) DEFAULT NULL,
  `values` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `cart_redis` */

/*Table structure for table `checkout_info` */

DROP TABLE IF EXISTS `checkout_info`;

CREATE TABLE `checkout_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `checkout` varchar(100) DEFAULT NULL,
  `cart` text,
  `cart_diff` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `checkout_info` */

/*Table structure for table `diff` */

DROP TABLE IF EXISTS `diff`;

CREATE TABLE `diff` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `diff_total_price` float DEFAULT NULL COMMENT '差价总价',
  `diff_total_skus` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '差价总sku,逗号分隔',
  `pay_real_sn` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '支付时传给收钱吧商城订单号 ',
  `diff_pay_flag` tinyint(2) DEFAULT '0' COMMENT '是否支付',
  `diff_pay_tid` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '支付交易号',
  `diff_pay_time` datetime DEFAULT NULL COMMENT '支付时间',
  `diff_oms_input_time` datetime DEFAULT NULL COMMENT 'oms推送过来的时间',
  `order_sn` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '订单号',
  `flag_push_oms` tinyint(2) DEFAULT '0' COMMENT '是否推送到oms',
  `push_oms_time` datetime DEFAULT NULL COMMENT '支付差价后推送到oms时间',
  `flag_expire` tinyint(2) DEFAULT '0' COMMENT '是否过期没有支付',
  `flag_expire_push_oms` tinyint(2) DEFAULT '0' COMMENT '过期是否推送给oms',
  `expire_push_oms_time` datetime DEFAULT NULL COMMENT '过期没有补差价，推送oms时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `diff_earn_points` float DEFAULT NULL COMMENT '差价的赚取的悦享钱',
  `diff_refund_flag` tinyint(2) DEFAULT '0' COMMENT '差价退款',
  `diff_refund_time` datetime DEFAULT NULL COMMENT '退款时间',
  `diff_refund_amount` float DEFAULT NULL COMMENT '退款金额',
  `diff_refund_times` int(11) DEFAULT '0' COMMENT '退款次数',
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `order_sn` (`order_sn`(191)) USING BTREE,
  KEY `pay_flag_index` (`diff_pay_flag`) USING BTREE,
  KEY `flag_push_oms_index` (`flag_push_oms`) USING BTREE,
  KEY `flag_expire_push_oms_index` (`flag_expire_push_oms`) USING BTREE,
  KEY `created_at_index` (`created_at`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;

/*Data for the table `diff` */

/*Table structure for table `diff_items` */

DROP TABLE IF EXISTS `diff_items`;

CREATE TABLE `diff_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_sn` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '订单号',
  `line_nbr` int(11) DEFAULT NULL COMMENT '订单行，对应oms',
  `sku` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'sku',
  `diff_price` float DEFAULT NULL COMMENT '差价',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `diff_text` varchar(500) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '差价新的sku的属性和值',
  `diff_refund_flag` tinyint(2) DEFAULT '0' COMMENT '差价退款',
  `diff_refund_time` datetime DEFAULT NULL COMMENT '退款时间',
  `diff_refund_amount` float DEFAULT NULL COMMENT '退款金额',
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `order_sn` (`order_sn`(191)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;

/*Data for the table `diff_items` */

/*Table structure for table `dmcs_message_mq` */

DROP TABLE IF EXISTS `dmcs_message_mq`;

CREATE TABLE `dmcs_message_mq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `order_sn` varchar(255) NOT NULL,
  `order_item_count` int(10) unsigned NOT NULL,
  `type` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `finished_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `dmcs_message_mq` */

/*Table structure for table `employee` */

DROP TABLE IF EXISTS `employee`;

CREATE TABLE `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `openid` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '微信openid',
  `wechat_id` int(11) DEFAULT NULL COMMENT 'wechat_id',
  `empid` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购编号',
  `store_code` varchar(200) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '导购门店id',
  `spuid` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '产品 唯一吗',
  `skuid` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'sku 唯一吗',
  `ordered_flag` tinyint(4) DEFAULT '0' COMMENT '0 未下单  1  已下单  2  失效',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `order_goods_id` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '订单行编号',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `openid_idx` (`openid`(191)) USING BTREE,
  KEY `empid_idx` (`empid`(191)) USING BTREE,
  KEY `spuid_idx` (`spuid`) USING BTREE,
  KEY `skuid_idx` (`skuid`) USING BTREE,
  KEY `flag_idx` (`ordered_flag`) USING BTREE,
  KEY `wechat_id_idx` (`wechat_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;

/*Data for the table `employee` */

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
  `condition_value` tinyint(1) DEFAULT NULL COMMENT '条件值',
  `max_num` int(10) DEFAULT NULL COMMENT '赠送上线',
  `step` tinyint(1) DEFAULT '0' COMMENT '是否多增   2   多赠   1 赠1个',
  `value_id` int(10) DEFAULT NULL COMMENT '优惠券/优惠码id',
  `type` tinyint(1) DEFAULT NULL COMMENT '类型   1   优惠码  2   优惠卷',
  `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='裂变表';

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='裂变奖品记录';

/*Data for the table `fission_award_log` */

/*Table structure for table `free_trial` */

DROP TABLE IF EXISTS `free_trial`;

CREATE TABLE `free_trial` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(255) DEFAULT NULL COMMENT '前台显示名称',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '1:未激活，2：激活,3:禁用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `add_sku` text COMMENT '条件-添加sku',
  `limited_qty` int(11) DEFAULT NULL COMMENT '限制商品加购数量',
  `money` decimal(4,2) NOT NULL COMMENT '邮费',
  `image` varchar(255) DEFAULT NULL COMMENT 'oss 图片地址',
  `state` varchar(20) DEFAULT NULL COMMENT '小程序版本 开发版develop 体验版trial 正式版release',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `name` (`display_name`),
  KEY `status_indx` (`status`),
  KEY `name_index` (`display_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='付邮试用';

/*Data for the table `free_trial` */

/*Table structure for table `items` */

DROP TABLE IF EXISTS `items`;

CREATE TABLE `items` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单id',
  `product_id` int(10) unsigned DEFAULT NULL COMMENT '商品id',
  `product_type` varchar(255) DEFAULT NULL COMMENT '商品类型',
  `product_options` varchar(255) DEFAULT NULL COMMENT '商品option',
  `sku` varchar(255) DEFAULT NULL COMMENT 'SKU',
  `image` varchar(255) DEFAULT NULL COMMENT '图片',
  `name` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `weight` int(10) unsigned DEFAULT NULL COMMENT '商品重量',
  `original_price` decimal(10,2) DEFAULT NULL COMMENT '原始价格',
  `price` decimal(10,2) DEFAULT NULL COMMENT '价格',
  `qty` int(10) unsigned DEFAULT NULL COMMENT '数量',
  `row_total` decimal(12,2) unsigned DEFAULT NULL COMMENT '小计',
  `discount_amount` varchar(255) DEFAULT NULL COMMENT '折扣',
  `applied_rule_ids` text COMMENT '规则id',
  `qty_refunded` int(10) unsigned DEFAULT NULL COMMENT '退货数量',
  `qty_shipped` int(10) unsigned DEFAULT NULL COMMENT '发货数量',
  `content` varchar(255) DEFAULT NULL COMMENT '刻字',
  `status` varchar(255) DEFAULT NULL COMMENT '订单内商品状态   ',
  PRIMARY KEY (`item_id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE,
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `items` */

/*Table structure for table `order_emails` */

DROP TABLE IF EXISTS `order_emails`;

CREATE TABLE `order_emails` (
  `id` int(1) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `email` char(50) DEFAULT NULL COMMENT 'email地址',
  `status` tinyint(1) DEFAULT '0' COMMENT '0  正常  1删除',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `order_emails` */

/*Table structure for table `order_gift` */

DROP TABLE IF EXISTS `order_gift`;

CREATE TABLE `order_gift` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_id` int(11) DEFAULT NULL COMMENT '订单id',
  `name` char(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名称',
  `image` char(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '图片',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='订单赠品表';

/*Data for the table `order_gift` */

/*Table structure for table `order_goods` */

DROP TABLE IF EXISTS `order_goods`;

CREATE TABLE `order_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单id',
  `group_id` int(10) DEFAULT NULL COMMENT '组id',
  `pdt_id` char(100) DEFAULT NULL COMMENT '商品id',
  `price_type` char(10) DEFAULT NULL COMMENT '商品价格类型  Y  计价 ',
  `product_type` char(50) DEFAULT NULL COMMENT '商品类型',
  `product_options` varchar(255) DEFAULT NULL COMMENT '商品option',
  `sku_id` char(100) DEFAULT NULL COMMENT 'SKU',
  `style_number` char(50) DEFAULT NULL COMMENT 'oms 锁库存时使用',
  `series` char(100) DEFAULT NULL COMMENT '前端显示系列名',
  `section` char(100) DEFAULT NULL COMMENT '前端 款号',
  `model_number` char(50) DEFAULT NULL COMMENT '提交订单锁库存 oms 使用 hybris sku编号',
  `image` varchar(255) DEFAULT NULL COMMENT '图片',
  `name` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `option` text COMMENT 'option 信息',
  `weight` varchar(20) DEFAULT '0' COMMENT '商品重量',
  `original_price` decimal(10,2) DEFAULT '0.00' COMMENT '原始价格',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `labor_price` decimal(10,2) DEFAULT NULL COMMENT '工费',
  `gold_price` decimal(10,2) DEFAULT '0.00' COMMENT '金价',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '优惠总金额',
  `inventory` int(10) unsigned DEFAULT '1' COMMENT '数量',
  `applied_rule_ids` text COMMENT '规则id',
  `qty_refunded` int(10) unsigned DEFAULT NULL COMMENT '退货数量',
  `qty_shipped` int(10) unsigned DEFAULT NULL COMMENT '发货数量',
  `lottery_font` tinyint(1) DEFAULT '1' COMMENT '刻字字体  字体，1roma2是arial',
  `content` varchar(255) DEFAULT NULL COMMENT '刻字',
  `pay_time` datetime DEFAULT NULL COMMENT '成功支付时间',
  `status` varchar(30) DEFAULT NULL COMMENT '订单内商品状态   ',
  `assr_status` varchar(30) DEFAULT NULL COMMENT '售后状态',
  `salesRetrnDte` datetime DEFAULT NULL COMMENT '退货退款日期',
  `oms_status` varchar(30) DEFAULT NULL COMMENT 'oms 订单行状态',
  `order_status` varchar(30) DEFAULT NULL COMMENT '订单状态',
  `invc_url` varchar(255) DEFAULT NULL COMMENT '电子发票',
  `policyUrl` varchar(255) DEFAULT NULL COMMENT '电子保单链接',
  `total` decimal(10,2) DEFAULT NULL COMMENT '付款金额',
  `pdttotal` decimal(10,2) DEFAULT NULL COMMENT '商品总价格',
  `promotion_type` char(10) DEFAULT NULL COMMENT '优惠类型 auto   pro    point',
  `promotion_discount` decimal(10,2) DEFAULT '0.00' COMMENT '已享受的，促销，打折，优惠金额',
  `promotion_discount_text` text COMMENT '已享受的，促销，打折，促销名字',
  `promotion_sale` decimal(10,2) DEFAULT '0.00' COMMENT '已享受的，促销，满减，优惠金额',
  `promotion_sale_text` text COMMENT '已享受的，促销，满减，促销名字',
  `promotion_point` decimal(10,2) DEFAULT '0.00' COMMENT '悦享钱抵扣金额',
  `promotion_member` decimal(10,2) DEFAULT '0.00' COMMENT '已享受 会员折扣优惠金额',
  `coupon` decimal(10,2) DEFAULT NULL COMMENT '已享受的，优惠券，优惠金额',
  `offer` decimal(10,2) DEFAULT NULL COMMENT '已享受的，优惠码，优惠金额',
  `saleTotal` decimal(10,2) DEFAULT NULL COMMENT '折扣总计',
  `sale_pdt` decimal(10,2) DEFAULT '0.00' COMMENT '商品折后价',
  `point` int(10) DEFAULT NULL COMMENT '使用悦享钱数量',
  `give_point` int(10) DEFAULT '0' COMMENT '获得悦享钱',
  `disparity` decimal(10,2) DEFAULT '0.00' COMMENT '差价',
  `shipping_id` varchar(100) DEFAULT NULL COMMENT '快递单号',
  `ship_method` char(10) DEFAULT NULL COMMENT '送货方式',
  `shipping_time` int(12) DEFAULT NULL COMMENT '发货时间',
  `is_gift` tinyint(1) DEFAULT '0' COMMENT '是否为赠品   1   是    0   不是',
  `is_service` tinyint(1) DEFAULT '0' COMMENT '是否可退换货   0  否  1  是',
  `channel` varchar(20) DEFAULT NULL COMMENT '渠道号',
  `guide` char(20) DEFAULT NULL COMMENT '导购id',
  `store_code` varchar(20) DEFAULT NULL COMMENT 'guide 对应的的门店号',
  `is_charme` tinyint(1) DEFAULT '0' COMMENT '0  IT   1  CHARMECORD',
  `is_presale` tinyint(1) DEFAULT '0' COMMENT '0 非预售  1  预售',
  `lineNbr` int(10) DEFAULT '0' COMMENT 'oms  取消订单   推送使用',
  `pick_code` varchar(20) DEFAULT NULL COMMENT '取货码',
  `description` text COMMENT '商品描述',
  `refund_amount` float(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `refund_time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '退款时间',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `diff_earn_points` int(10) DEFAULT '0' COMMENT '补差价之后，重新计算的行获取的悦享钱',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `order_goods` */

/*Table structure for table `order_rate_config` */

DROP TABLE IF EXISTS `order_rate_config`;

CREATE TABLE `order_rate_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rate` varchar(255) DEFAULT NULL COMMENT '销售目标',
  `days` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `order_rate_config` */

/*Table structure for table `order_status_history` */

DROP TABLE IF EXISTS `order_status_history`;

CREATE TABLE `order_status_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_id` int(10) unsigned DEFAULT NULL COMMENT '订单id',
  `status` varchar(30) DEFAULT NULL COMMENT 'oms 订单状态',
  `oms_status` varchar(30) DEFAULT NULL COMMENT 'oms 订单状态',
  `comment` text COMMENT '备注',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE,
  CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `order_status_history` */

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `order_sn` varchar(255) DEFAULT NULL COMMENT '订单编号',
  `pay_real_sn` varchar(255) DEFAULT NULL COMMENT '支付真实订单号',
  `status` varchar(50) DEFAULT NULL COMMENT '订单状态 （pay/send/receive/service）',
  `oms_status` varchar(255) DEFAULT NULL COMMENT 'oms 订单状态',
  `applied_rule_ids` varchar(255) DEFAULT NULL COMMENT '促销规则ID',
  `total_qty_ordered` int(10) unsigned DEFAULT NULL COMMENT '订单商品数量',
  `shipping_amount` decimal(12,2) DEFAULT NULL COMMENT '运费',
  `wechat_id` char(50) DEFAULT NULL COMMENT '微信 wechat id ',
  `customer_id` char(50) DEFAULT NULL COMMENT '用户ID',
  `customer_name` varchar(255) DEFAULT NULL COMMENT '用户名称',
  `customer_gender` varchar(255) DEFAULT NULL COMMENT '性别',
  `customer_salute` varchar(20) DEFAULT NULL COMMENT '称谓',
  `customer_level` varchar(255) DEFAULT NULL COMMENT '用户级别',
  `customer_note_notify` text COMMENT '用户留言',
  `service_type` varchar(50) DEFAULT NULL COMMENT '配送方式(分店取货，送货上门)',
  `store_id` varchar(20) DEFAULT NULL COMMENT '门店',
  `store_name` varchar(255) DEFAULT NULL COMMENT '分店名称',
  `store_address` text COMMENT '分店地址',
  `store_open_hour` varchar(255) DEFAULT NULL COMMENT '门店营业描述',
  `store_mobile` varchar(255) DEFAULT NULL COMMENT '门店手机号 可多个',
  `shipping_id` varchar(100) DEFAULT NULL COMMENT '快递单号',
  `shipping_description` varchar(255) DEFAULT NULL COMMENT '货运描述',
  `ship_method` varchar(255) DEFAULT NULL COMMENT '快递公司',
  `shipping_time` int(12) DEFAULT NULL COMMENT '发货时间',
  `consignee` varchar(255) DEFAULT NULL COMMENT '收货人姓名',
  `province` varchar(255) DEFAULT NULL COMMENT '省',
  `city` varchar(255) DEFAULT NULL COMMENT '市',
  `district` varchar(255) DEFAULT NULL COMMENT '区',
  `address` varchar(255) DEFAULT NULL COMMENT '用户地址',
  `phone` varchar(255) DEFAULT NULL COMMENT '用户电话',
  `phone_code` char(10) DEFAULT NULL COMMENT '电话区号',
  `postal_code` varchar(20) DEFAULT NULL COMMENT '邮编',
  `pay_method` varchar(255) DEFAULT NULL COMMENT '支付方式',
  `pay_code` varchar(255) DEFAULT NULL COMMENT '支付状态',
  `transaction_id` varchar(50) DEFAULT NULL COMMENT '交易号',
  `pay_time` datetime DEFAULT NULL COMMENT '支付成功时间',
  `refund_time` datetime DEFAULT NULL COMMENT '退款时间',
  `refund_times` int(2) DEFAULT '0' COMMENT '退款次数',
  `refund_amount` decimal(10,2) DEFAULT '0.00' COMMENT '退款金额',
  `invc_url` varchar(255) DEFAULT NULL COMMENT '电子发票',
  `policyUrl` varchar(255) DEFAULT NULL COMMENT '电子保单',
  `invoice_title` varchar(255) DEFAULT NULL COMMENT '发票标题',
  `invoice_code` char(50) DEFAULT NULL COMMENT '发票识别码',
  `invoice_type` tinyint(1) DEFAULT '1' COMMENT '0  单位 1 个人',
  `total_amount` decimal(10,2) DEFAULT NULL COMMENT '付款金额',
  `pdttotal` decimal(10,2) DEFAULT NULL COMMENT '商品总价格',
  `promotionDiscount` decimal(10,2) DEFAULT NULL COMMENT '已享受的，促销，打折，优惠金额',
  `promotionSale` decimal(10,2) DEFAULT NULL COMMENT '已享受的，促销，满减，优惠金额',
  `coupon_id` int(10) DEFAULT NULL COMMENT '优惠券id',
  `coupon_discount` decimal(10,2) DEFAULT NULL COMMENT '已享受的，优惠券，优惠金额',
  `code_id` char(10) DEFAULT NULL COMMENT '优惠码',
  `code_discount` decimal(10,2) DEFAULT '0.00' COMMENT '已享受的，优惠码，优惠金额',
  `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣总计',
  `give_point` decimal(10,2) DEFAULT NULL COMMENT '获得悦享钱数量',
  `point_id` int(10) DEFAULT NULL COMMENT '悦享钱应用规则id',
  `point` decimal(10,2) DEFAULT '0.00' COMMENT '使用悦享钱',
  `point_discount` decimal(10,2) DEFAULT '0.00' COMMENT '悦享钱优惠金额',
  `member_discount` decimal(10,2) DEFAULT '0.00' COMMENT '会员优惠金额',
  `sale_pdt` decimal(10,2) DEFAULT '0.00' COMMENT '商品折后价',
  `disparity` decimal(10,2) DEFAULT '0.00' COMMENT '差价',
  `gift_content` varchar(255) DEFAULT NULL COMMENT '礼品卡内容',
  `gift_from` char(50) DEFAULT NULL COMMENT '礼品卡发件人',
  `gift_to` char(50) DEFAULT NULL COMMENT '礼品卡收件人',
  `remote_ip` varchar(255) DEFAULT NULL COMMENT '远程用户ip',
  `pick_code` varchar(255) DEFAULT NULL COMMENT '取货码',
  `cron_status` tinyint(1) DEFAULT '0' COMMENT '1   需要取消   2  需要更改为已付款   0 无需操作',
  `channel` varchar(20) DEFAULT '0' COMMENT '渠道id',
  `gold_price` text COMMENT '下单时金价，0228新增',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '订单创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '订单更新时间',
  `payment_at` timestamp NULL DEFAULT NULL COMMENT '调起支付信息过期时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `custid` (`customer_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `orders` */

/*Table structure for table `prod_order_collects` */

DROP TABLE IF EXISTS `prod_order_collects`;

CREATE TABLE `prod_order_collects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_money` varchar(255) DEFAULT '0' COMMENT '销售额',
  `uv` int(10) DEFAULT NULL,
  `pv` int(10) DEFAULT '0' COMMENT '订单量',
  `days` varchar(20) DEFAULT NULL COMMENT '时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `prod_order_collects` */

/*Table structure for table `subscribe_shipped` */

DROP TABLE IF EXISTS `subscribe_shipped`;

CREATE TABLE `subscribe_shipped` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `openid` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '订单id',
  `order_sn` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '订单编号',
  `template_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '模板id',
  `template_status` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT 'accept、reject、ban',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '模板状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

/*Data for the table `subscribe_shipped` */

/*Table structure for table `timing_guide_ranking` */

DROP TABLE IF EXISTS `timing_guide_ranking`;

CREATE TABLE `timing_guide_ranking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `guide_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '导购id',
  `store_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '城市id',
  `guide_name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '导购名称',
  `store_name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '门店名称',
  `city_name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '城市名称',
  `money` float(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '金额',
  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '日期',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1-导购  2-门店  3-城市',
  `created_at` datetime(6) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `timing_guide_ranking` */

/*Table structure for table `upay_info` */

DROP TABLE IF EXISTS `upay_info`;

CREATE TABLE `upay_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `terminal_sn` varchar(255) DEFAULT NULL COMMENT '终端号',
  `terminal_key` varchar(255) DEFAULT NULL COMMENT '终端密钥',
  `store_id` varchar(255) DEFAULT NULL COMMENT '门店id',
  `code` varchar(255) DEFAULT NULL COMMENT 'code码',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `upay_info` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
