/*
SQLyog Ultimate v12.08 (64 bit)
MySQL - 5.7.26 : Database - el_goods
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`el_goods` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */;

USE `el_goods`;

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

/*Table structure for table `oms_order_status` */

DROP TABLE IF EXISTS `oms_order_status`;

CREATE TABLE `oms_order_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键Id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态（客户）',
  `state` tinyint(3) NOT NULL DEFAULT '0' COMMENT '订单状态（OMS）',
  `status_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '对应客户订单状态名称',
  `state_name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '对应OMS订单状态名称',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '记录更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_state_unique` (`status`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单状态管理';

/*Data for the table `oms_order_status` */

/*Table structure for table `oms_sync_log` */

DROP TABLE IF EXISTS `oms_sync_log`;

CREATE TABLE `oms_sync_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) DEFAULT '0' COMMENT '是否处理',
  `content` text COLLATE utf8mb4_bin NOT NULL COMMENT 'json字符串e.g:{sku1:stock,sku2:stock}',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(20) COLLATE utf8mb4_bin NOT NULL COMMENT '同步类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Data for the table `oms_sync_log` */

/*Table structure for table `sales_volume` */

DROP TABLE IF EXISTS `sales_volume`;

CREATE TABLE `sales_volume` (
  `spu_id` int(10) NOT NULL COMMENT 'spu主键',
  `volume` int(10) DEFAULT '0',
  PRIMARY KEY (`spu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Data for the table `sales_volume` */

/*Table structure for table `skin_report` */

DROP TABLE IF EXISTS `skin_report`;

CREATE TABLE `skin_report` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `openid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户openid',
  `original_report_url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '原始报告OSS的URL',
  `fiveAgeData` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '五维图年龄',
  `fiveGloss` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '五维图光泽度',
  `fiveStains` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '五维图色斑',
  `fiveDarkCirclesData` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '五维图黑眼圈',
  `fiveWrinkleData` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '五维图皱纹',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  `q1` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第一题选择',
  `q2` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第二题选择',
  `q3` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三题选择',
  `q4` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第四题选择',
  `answer_time` datetime DEFAULT NULL COMMENT '补充回答问题时间',
  `skin_image_url` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户肌肤测试原始图片地址',
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `skin_report` */

/*Table structure for table `tb_ad_item` */

DROP TABLE IF EXISTS `tb_ad_item`;

CREATE TABLE `tb_ad_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loc_id` int(11) NOT NULL COMMENT '关联ad的id',
  `name` varchar(2000) DEFAULT NULL COMMENT '广告位名字',
  `img` varchar(255) DEFAULT NULL COMMENT '图片',
  `link` varchar(2000) DEFAULT NULL COMMENT '链接',
  `start_time` int(11) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(11) DEFAULT NULL COMMENT '结束时间',
  `data1` text,
  `data2` text,
  `data3` text,
  `data4` text,
  `data5` text,
  `data6` text,
  `data7` text,
  `data8` text,
  `data9` text,
  `data10` text,
  `tags` varchar(255) DEFAULT NULL COMMENT '标签，为了以后搜索用',
  `userid` int(11) NOT NULL DEFAULT '0' COMMENT '后台管理人员id',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，默认0未启用，1启用',
  `update_stamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `asort` int(11) DEFAULT NULL COMMENT '排序值',
  `img_size` varchar(255) DEFAULT NULL COMMENT '图片大小以*号隔开width*height',
  `file_md5` varchar(50) DEFAULT NULL COMMENT '图片的MD5加密文',
  PRIMARY KEY (`id`),
  KEY `update_stamp` (`update_stamp`),
  KEY `ix_loc_id` (`loc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='位置内容管理{光鹏杰}';

/*Data for the table `tb_ad_item` */

/*Table structure for table `tb_ad_location` */

DROP TABLE IF EXISTS `tb_ad_location`;

CREATE TABLE `tb_ad_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `num` int(11) DEFAULT '1' COMMENT '广告位数量',
  `userid` int(11) NOT NULL COMMENT '后台管理人员id',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态，默认0未启用，1启用',
  `start_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `update_stamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '说明',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `update_stamp` (`update_stamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='需求位置管理{光鹏杰}';

/*Data for the table `tb_ad_location` */

/*Table structure for table `tb_blacklist` */

DROP TABLE IF EXISTS `tb_blacklist`;

CREATE TABLE `tb_blacklist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `word` varchar(200) NOT NULL COMMENT '词',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_blacklist` */

/*Table structure for table `tb_category` */

DROP TABLE IF EXISTS `tb_category`;

CREATE TABLE `tb_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `cat_name` varchar(100) NOT NULL COMMENT '类目名称',
  `cat_name_en` varchar(100) DEFAULT '' COMMENT '类目英文名称',
  `parent_cat_id` int(11) DEFAULT '0' COMMENT '父类目ID',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 0删除 1正常',
  `cat_kv_image` varchar(1000) DEFAULT '' COMMENT '分类头图',
  `share_content` varchar(2000) DEFAULT NULL COMMENT '分享文案',
  `share_image` varchar(1000) DEFAULT '' COMMENT '分享图',
  `cat_desc` varchar(1000) NOT NULL DEFAULT '' COMMENT '类目描述',
  `cat_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类目类型 1正常类目 2活动类目',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序值',
  `cat_code` varchar(100) DEFAULT NULL COMMENT '分类CODE',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Sisley类目信息表';

/*Data for the table `tb_category` */

/*Table structure for table `tb_collection_chunk` */

DROP TABLE IF EXISTS `tb_collection_chunk`;

CREATE TABLE `tb_collection_chunk` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `collection_id` int(11) NOT NULL COMMENT '集合ID',
  `chunk_id` int(11) NOT NULL COMMENT '块ID',
  `type` tinyint(1) NOT NULL COMMENT '0普通商品 1赠品',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `collection_id` (`collection_id`,`chunk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='集合块 一个集合多个块，一个块一个商品';

/*Data for the table `tb_collection_chunk` */

/*Table structure for table `tb_collection_detail` */

DROP TABLE IF EXISTS `tb_collection_detail`;

CREATE TABLE `tb_collection_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `product_idx` int(11) NOT NULL COMMENT '产品自增ID',
  `channel` varchar(20) NOT NULL COMMENT '渠道',
  `detail` text COMMENT '详情',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_idx` (`product_idx`,`channel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='商品详情表';

/*Data for the table `tb_collection_detail` */

/*Table structure for table `tb_collection_relation` */

DROP TABLE IF EXISTS `tb_collection_relation`;

CREATE TABLE `tb_collection_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `collection_id` int(11) NOT NULL COMMENT '商品集合ID',
  `sku_id` varchar(100) NOT NULL COMMENT '规格ID',
  `chunk_id` int(11) NOT NULL COMMENT '块ID',
  `custom_product_name` varchar(100) DEFAULT NULL COMMENT '自定义产品名称',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `collection_id` (`collection_id`,`chunk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Sisley商品集合-规格 关联表';

/*Data for the table `tb_collection_relation` */

/*Table structure for table `tb_prod_cat_relation` */

DROP TABLE IF EXISTS `tb_prod_cat_relation`;

CREATE TABLE `tb_prod_cat_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `product_idx` varchar(100) NOT NULL COMMENT '产品自增ID',
  `cat_id` int(11) NOT NULL COMMENT '类目ID',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `type` tinyint(1) DEFAULT '1' COMMENT '1商品 2商品集合',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序值',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cat_id` (`cat_id`,`product_idx`,`type`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Sisley商品-类目 关联表';

/*Data for the table `tb_prod_cat_relation` */

/*Table structure for table `tb_prod_collection` */

DROP TABLE IF EXISTS `tb_prod_collection`;

CREATE TABLE `tb_prod_collection` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `colle_name` varchar(200) DEFAULT NULL COMMENT '商品集合名称',
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属类目ID',
  `colle_desc` text COMMENT '产品规格描述',
  `ori_price` decimal(10,2) DEFAULT NULL COMMENT '市场价格',
  `price` decimal(10,2) DEFAULT NULL COMMENT '售价',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 下架 1上架 2售罄',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `priority_cat_id` int(11) NOT NULL DEFAULT '0' COMMENT '优先目录',
  `kv_images` text COMMENT '产品头图',
  `detail_images` text COMMENT '产品图文详情',
  `short_colle_desc` varchar(1000) DEFAULT '' COMMENT '商品集合短描述',
  `colle_id` varchar(100) NOT NULL DEFAULT '' COMMENT '套装ID',
  `tag` varchar(500) DEFAULT '' COMMENT '标签',
  `rec_cat_id` varchar(200) NOT NULL DEFAULT '' COMMENT '为你推荐类目ID',
  `can_search` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可搜索',
  `display_start_time` int(11) NOT NULL DEFAULT '0' COMMENT '展示开始时间',
  `display_end_time` int(11) NOT NULL DEFAULT '0' COMMENT '展示结束时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Sisley商品集合表';

/*Data for the table `tb_prod_collection` */

/*Table structure for table `tb_prod_sku` */

DROP TABLE IF EXISTS `tb_prod_sku`;

CREATE TABLE `tb_prod_sku` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `sku_id` varchar(100) NOT NULL COMMENT 'sku',
  `spec_color_code` varchar(100) DEFAULT NULL COMMENT '【规格】颜色code',
  `spec_capacity_ml_code` varchar(100) DEFAULT NULL COMMENT '【规格】容量ml code',
  `spec_capacity_g_code` varchar(100) DEFAULT NULL COMMENT '【规格】容量g code',
  `ori_price` decimal(10,2) DEFAULT '0.00' COMMENT '原价',
  `kv_images` text COMMENT '缩略图',
  `boms` text COMMENT '物料原始数据',
  `product_idx` int(11) NOT NULL COMMENT '产品自增ID',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `spec_color_code_desc` varchar(100) DEFAULT NULL COMMENT '【规格】颜色code 描述',
  `spec_capacity_ml_code_desc` varchar(100) DEFAULT NULL COMMENT '【规格】容量ml code 描述',
  `spec_capacity_g_code_desc` varchar(100) DEFAULT NULL COMMENT '【规格】容量g code 描述',
  `contained_sku_ids` varchar(500) DEFAULT '' COMMENT '如果是固定礼盒，会包含sku ids,用于展示',
  `revenue_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '税收类型 1护肤用化妆品 2护发用化妆品 3刷子类制品 4美容修饰类化妆品',
  `size` varchar(100) NOT NULL DEFAULT '' COMMENT '尺寸',
  `control_stock` tinyint(1) DEFAULT '0' COMMENT '是否可手动配置库存',
  `include_skus` varchar(100) DEFAULT '' COMMENT '包含的sku',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku_id` (`sku_id`),
  KEY `product_idx` (`product_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_prod_sku` */

insert  into `tb_prod_sku`(`id`,`sku_id`,`spec_color_code`,`spec_capacity_ml_code`,`spec_capacity_g_code`,`ori_price`,`kv_images`,`boms`,`product_idx`,`created_at`,`updated_at`,`deleted_at`,`spec_color_code_desc`,`spec_capacity_ml_code_desc`,`spec_capacity_g_code_desc`,`contained_sku_ids`,`revenue_type`,`size`,`control_stock`,`include_skus`) values (1,'11314429',NULL,NULL,NULL,'342.00',NULL,NULL,1,'2021-05-27 13:16:47','2021-05-27 13:16:47',NULL,'','','',NULL,1,'',0,NULL);

/*Table structure for table `tb_product` */

DROP TABLE IF EXISTS `tb_product`;

CREATE TABLE `tb_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `product_id` varchar(100) NOT NULL COMMENT '产品ID',
  `product_name` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `product_name_en` varchar(100) DEFAULT NULL COMMENT '产品名称',
  `custom_product_name` varchar(100) DEFAULT NULL COMMENT '自定义产品名称',
  `custom_product_name_en` varchar(100) DEFAULT NULL COMMENT '自定义产品名称',
  `custom_keyword` varchar(1000) DEFAULT '' COMMENT '自定义搜索关键字',
  `brand` varchar(100) DEFAULT NULL COMMENT '品牌中文',
  `brand_code` varchar(100) DEFAULT NULL COMMENT '品牌code',
  `spec_type` varchar(150) DEFAULT '' COMMENT '规格类型',
  `product_desc` varchar(1000) DEFAULT '' COMMENT '产品描述',
  `priority_cat_id` int(11) NOT NULL DEFAULT '0' COMMENT '优先目录',
  `kv_images` text COMMENT '产品头图',
  `detail_images` text COMMENT '产品图文详情',
  `status` tinyint(1) DEFAULT '2' COMMENT '商品状态 1上架 2下架 3删除',
  `type` tinyint(1) DEFAULT '1' COMMENT '1正常商品 2预售商品',
  `insert_type` tinyint(1) DEFAULT '0' COMMENT '添加类型 0导入 1手动添加',
  `presale_at` varchar(100) DEFAULT NULL COMMENT '预售信息',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `short_product_desc` text COMMENT '产品短描述',
  `tag` varchar(500) DEFAULT '' COMMENT '标签',
  `rec_cat_id` varchar(200) NOT NULL DEFAULT '' COMMENT '为你推荐类目ID',
  `can_search` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可搜索',
  `can_share` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可分享',
  `display_start_time` int(11) NOT NULL DEFAULT '0' COMMENT '展示开始时间',
  `display_end_time` int(11) NOT NULL DEFAULT '0' COMMENT '展示结束时间',
  `share_img` varchar(150) DEFAULT NULL COMMENT '分享图片',
  `list_img` varchar(150) DEFAULT NULL COMMENT '列表图片',
  `is_gift_box` tinyint(1) DEFAULT '0' COMMENT '是否是礼盒',
  `sort` int(10) DEFAULT '0' COMMENT '排序值',
  `rec_spu` varchar(200) DEFAULT NULL COMMENT '推荐商品',
  `score` int(2) DEFAULT '0' COMMENT '分数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`),
  KEY `insert_type` (`insert_type`),
  KEY `status` (`status`),
  KEY `brand_code` (`brand_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='商品表';

/*Data for the table `tb_product` */

insert  into `tb_product`(`id`,`product_id`,`product_name`,`product_name_en`,`custom_product_name`,`custom_product_name_en`,`custom_keyword`,`brand`,`brand_code`,`spec_type`,`product_desc`,`priority_cat_id`,`kv_images`,`detail_images`,`status`,`type`,`insert_type`,`presale_at`,`created_at`,`updated_at`,`deleted_at`,`short_product_desc`,`tag`,`rec_cat_id`,`can_search`,`can_share`,`display_start_time`,`display_end_time`,`share_img`,`list_img`,`is_gift_box`,`sort`,`rec_spu`,`score`) values (1,'EL_11314429','奥伦纳素黑皂','',NULL,NULL,'',NULL,NULL,'','',0,NULL,NULL,0,1,1,NULL,'2021-05-27 13:12:03','2021-05-27 13:12:03',NULL,'奥伦纳素黑皂','','',1,1,0,0,'','http://el.admin.local/dlc_statics/ry4VDTd7wrHzOAZN4Uw1I3EbOXIkB5NMeUqfNhLw.jpeg',0,0,NULL,0);

/*Table structure for table `tb_product_detail` */

DROP TABLE IF EXISTS `tb_product_detail`;

CREATE TABLE `tb_product_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `product_idx` int(11) NOT NULL COMMENT '产品自增ID',
  `channel` varchar(20) NOT NULL COMMENT '渠道',
  `detail` text COMMENT '详情',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_idx` (`product_idx`,`channel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='商品详情表';

/*Data for the table `tb_product_detail` */

/*Table structure for table `tb_product_image` */

DROP TABLE IF EXISTS `tb_product_image`;

CREATE TABLE `tb_product_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) DEFAULT NULL,
  `ori_price` varchar(10) DEFAULT NULL,
  `image` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `tb_product_image` */

/*Table structure for table `tb_recommend` */

DROP TABLE IF EXISTS `tb_recommend`;

CREATE TABLE `tb_recommend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `cat_id` int(11) NOT NULL COMMENT '类目ID',
  `flag` varchar(100) NOT NULL COMMENT '标识 唯一',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `rec_desc` varchar(200) NOT NULL DEFAULT '' COMMENT '推荐描述',
  PRIMARY KEY (`id`),
  UNIQUE KEY `flag` (`flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='商品推荐';

/*Data for the table `tb_recommend` */

/*Table structure for table `tb_search_redirect` */

DROP TABLE IF EXISTS `tb_search_redirect`;

CREATE TABLE `tb_search_redirect` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `word` varchar(200) NOT NULL COMMENT '词',
  `type` varchar(200) NOT NULL COMMENT '跳转类型 1商品详情页 2商品列表页 3专题页',
  `code` varchar(100) NOT NULL COMMENT 'code码 商品ID/类目ID/专题ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0失效 1正常',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `word` (`word`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_search_redirect` */

/*Table structure for table `tb_search_synonym` */

DROP TABLE IF EXISTS `tb_search_synonym`;

CREATE TABLE `tb_search_synonym` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `word` varchar(200) NOT NULL COMMENT '词',
  `convert_word` varchar(200) NOT NULL COMMENT '转换词',
  `remark` varchar(1000) NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_search_synonym` */

/*Table structure for table `tb_spec` */

DROP TABLE IF EXISTS `tb_spec`;

CREATE TABLE `tb_spec` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `spec_code` varchar(100) NOT NULL COMMENT '规格code',
  `spec_unit` varchar(50) DEFAULT '' COMMENT '规格单位',
  `spec_property` varchar(100) DEFAULT '' COMMENT '规格属性',
  `spec_type` varchar(100) NOT NULL COMMENT '规格类型',
  `spec_desc` varchar(100) NOT NULL COMMENT '规格描述',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '商品状态 0下架 1上架 2删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `spec_type` (`spec_type`,`spec_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='规格表';

/*Data for the table `tb_spec` */

/*Table structure for table `tb_stock_log` */

DROP TABLE IF EXISTS `tb_stock_log`;

CREATE TABLE `tb_stock_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(2) DEFAULT '0' COMMENT '1入库2下单扣除 3 订单失效返还 4 残次品',
  `channel_id` tinyint(2) DEFAULT NULL COMMENT '0 共享 其他数字代表渠道',
  `sku_id` varchar(20) NOT NULL COMMENT 'skuid',
  `inventory_pos_status` tinyint(1) DEFAULT '0',
  `order_pos_status` tinyint(1) DEFAULT '0',
  `num` int(11) DEFAULT '0' COMMENT '数量',
  `batch_no` varchar(20) DEFAULT '' COMMENT '批次',
  `order_no` varchar(20) DEFAULT '' COMMENT '子订单号 退单时记录',
  `note` varchar(100) DEFAULT '' COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ori_num` int(10) DEFAULT '0' COMMENT '原始数量(全量更新时会用到)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='库存流水记录';

/*Data for the table `tb_stock_log` */

/*Table structure for table `tb_warehose_log` */

DROP TABLE IF EXISTS `tb_warehose_log`;

CREATE TABLE `tb_warehose_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `goods_name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '商品名称',
  `is_auto` tinyint(1) DEFAULT '0' COMMENT '自动推送',
  `status` tinyint(1) DEFAULT '0' COMMENT '0初始化 1 已入库 2自动 3残次',
  `branch` varchar(30) COLLATE utf8mb4_bin DEFAULT NULL COMMENT '批次',
  `ready_number` int(10) DEFAULT NULL COMMENT '原始数量',
  `actual_number` int(10) DEFAULT NULL COMMENT '实际数量',
  `diff_number` int(10) DEFAULT NULL COMMENT '差异',
  `remark` varchar(1000) COLLATE utf8mb4_bin DEFAULT '' COMMENT '备注',
  `warehose_at` date DEFAULT NULL COMMENT '仓库到货日期',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

/*Data for the table `tb_warehose_log` */

/*Table structure for table `wechat_access_token` */

DROP TABLE IF EXISTS `wechat_access_token`;

CREATE TABLE `wechat_access_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(100) NOT NULL COMMENT '谁的Token',
  `token` text NOT NULL COMMENT 'AccessToken值',
  `expired_time` timestamp NULL DEFAULT NULL COMMENT '过期时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `wechat_access_token` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
