-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2020 广01 朿03 旿14:58
-- 服务器版本: 5.7.27
-- PHP 版本: 7.2.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `product_css`
--

-- --------------------------------------------------------

--
-- 表的结构 `css_brand`
--

DROP TABLE IF EXISTS `css_brand`;
CREATE TABLE IF NOT EXISTS `css_brand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_categories_info`
--

DROP TABLE IF EXISTS `css_categories_info`;
CREATE TABLE IF NOT EXISTS `css_categories_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` varchar(100) NOT NULL,
  `category_code` varchar(100) NOT NULL,
  `p_cate_id` int(11) DEFAULT '0',
  `level` tinyint(1) DEFAULT '1',
  `init_code` varchar(100) DEFAULT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `category_kv_image` text,
  `share_content` varchar(100) DEFAULT NULL,
  `share_image` text,
  `init_prods` text,
  `include_style_number` text,
  `exclude_style_number` text,
  `selected_items` text,
  `custom_prod_type` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `available` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_code`),
  KEY `init_code` (`init_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_cate_prod_relation`
--

DROP TABLE IF EXISTS `css_cate_prod_relation`;
CREATE TABLE IF NOT EXISTS `css_cate_prod_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(100) NOT NULL,
  `category_idx` int(11) NOT NULL,
  `product_idx` int(11) NOT NULL,
  `sort` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `category_idx` (`category_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_ec_skus_info`
--

DROP TABLE IF EXISTS `css_ec_skus_info`;
CREATE TABLE IF NOT EXISTS `css_ec_skus_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) NOT NULL,
  `master_catalog_item` varchar(100) NOT NULL,
  `sub_skus` text,
  `model_number` varchar(200) DEFAULT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `price_type` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `labor_price` decimal(10,2) DEFAULT NULL,
  `usage_code` varchar(100) DEFAULT NULL,
  `size` varchar(100) DEFAULT NULL,
  `ring_size` varchar(100) DEFAULT NULL,
  `product_part` varchar(100) DEFAULT NULL,
  `earrings` varchar(100) DEFAULT NULL,
  `weight` varchar(100) DEFAULT NULL,
  `style` varchar(100) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  `metarial` varchar(100) DEFAULT NULL,
  `length` varchar(100) DEFAULT NULL,
  `gauge` varchar(100) DEFAULT NULL,
  `cstandard` varchar(100) DEFAULT NULL COMMENT '自定义规格字段',
  `thumbnail` text,
  `boms` text,
  `can_letter` tinyint(1) DEFAULT '0',
  `letter_info` varchar(100) DEFAULT NULL,
  `certificates` text,
  `spec_name` varchar(255) DEFAULT NULL,
  `sku_spec` text,
  `sku_type` tinyint(1) DEFAULT '0' COMMENT '0:基础SKU,1:虚拟SKU',
  `special_type` tinyint(1) DEFAULT '0' COMMENT '1:charme,2:presale',
  `has_main_material` tinyint(4) DEFAULT '0',
  `diamond_set` varchar(100) DEFAULT NULL,
  `final_boms` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_inventory_details`
--

DROP TABLE IF EXISTS `css_inventory_details`;
CREATE TABLE IF NOT EXISTS `css_inventory_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) DEFAULT NULL,
  `inventory_id` varchar(100) NOT NULL,
  `master_catalog_item` varchar(100) DEFAULT NULL,
  `model_sequence_number` varchar(100) DEFAULT NULL,
  `item_number` varchar(20) DEFAULT NULL,
  `department_code` varchar(100) DEFAULT NULL,
  `detail` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_id` (`inventory_id`),
  KEY `sku` (`sku`),
  KEY `master_catalog_item` (`master_catalog_item`),
  KEY `model_sequence_number` (`model_sequence_number`),
  KEY `item_number` (`item_number`),
  KEY `department_code` (`department_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_materials`
--

DROP TABLE IF EXISTS `css_materials`;
CREATE TABLE IF NOT EXISTS `css_materials` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_oms_details`
--

DROP TABLE IF EXISTS `css_oms_details`;
CREATE TABLE IF NOT EXISTS `css_oms_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) DEFAULT NULL,
  `model_sequence_number` varchar(100) DEFAULT NULL,
  `master_catalog_item` varchar(100) DEFAULT NULL,
  `detail` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_oms_prices`
--

DROP TABLE IF EXISTS `css_oms_prices`;
CREATE TABLE IF NOT EXISTS `css_oms_prices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) DEFAULT NULL,
  `detail` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_pim_details`
--

DROP TABLE IF EXISTS `css_pim_details`;
CREATE TABLE IF NOT EXISTS `css_pim_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(100) NOT NULL,
  `master_catalog_item` varchar(100) NOT NULL,
  `model_sequence_number` varchar(100) NOT NULL,
  `detail` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `master_catalog_item` (`master_catalog_item`),
  KEY `model_sequence_number` (`model_sequence_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9556 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_products_info`
--

DROP TABLE IF EXISTS `css_products_info`;
CREATE TABLE IF NOT EXISTS `css_products_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(100) NOT NULL,
  `master_catalog_item` varchar(100) NOT NULL,
  `gold_type_code` varchar(100) DEFAULT NULL,
  `gold_type` varchar(100) DEFAULT NULL,
  `material_code` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `style_category_number` varchar(20) DEFAULT NULL COMMENT '款号',
  `style_number` varchar(30) DEFAULT NULL,
  `usage_code` varchar(100) DEFAULT NULL COMMENT '用途code',
  `usage` varchar(100) DEFAULT NULL COMMENT '用途中文',
  `product_type` varchar(20) DEFAULT NULL,
  `custom_product_type` text,
  `sub_prods` text,
  `product_name` varchar(100) DEFAULT NULL,
  `custom_product_name` varchar(100) DEFAULT NULL,
  `custom_keyword` text,
  `special` varchar(100) DEFAULT NULL COMMENT '特殊商品',
  `brand` varchar(100) DEFAULT NULL,
  `brand_code` varchar(100) DEFAULT NULL,
  `collection_name` varchar(100) DEFAULT NULL,
  `collection_code` varchar(100) DEFAULT NULL,
  `sub_collection_name` varchar(100) DEFAULT NULL,
  `sub_collection_code` varchar(100) DEFAULT NULL,
  `product_description` text,
  `price_type` varchar(20) DEFAULT NULL,
  `kv_images` text,
  `detail_images` text,
  `type` tinyint(1) DEFAULT '1',
  `related_prod` varchar(100) DEFAULT NULL,
  `is_brandsite` tinyint(1) DEFAULT '0',
  `is_noauto` tinyint(1) DEFAULT '0' COMMENT '是否为手动创建的产品',
  `special_type` tinyint(1) DEFAULT '0' COMMENT '1:charme,2:presale',
  `is_specDisplay` tinyint(1) DEFAULT '0' COMMENT '是否为特殊初始化的产品',
  `presale_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `display_status` tinyint(1) DEFAULT '0',
  `qr_code` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`),
  KEY `product_type` (`product_type`),
  KEY `is_brandsite` (`is_brandsite`),
  KEY `is_noauto` (`is_noauto`),
  KEY `display_status` (`display_status`),
  KEY `usage_code` (`usage_code`),
  KEY `brand_code` (`brand_code`),
  KEY `collection_code` (`collection_code`),
  KEY `sub_collection_code` (`sub_collection_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_prod_d_sku_relation`
--

DROP TABLE IF EXISTS `css_prod_d_sku_relation`;
CREATE TABLE IF NOT EXISTS `css_prod_d_sku_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(100) NOT NULL,
  `product_idx` int(11) NOT NULL,
  `sku_idx` int(11) NOT NULL,
  `sort` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `product_idx` (`product_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_prod_sku_relation`
--

DROP TABLE IF EXISTS `css_prod_sku_relation`;
CREATE TABLE IF NOT EXISTS `css_prod_sku_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(100) NOT NULL,
  `product_idx` int(11) NOT NULL,
  `sku_idx` int(11) NOT NULL,
  `sort` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `product_idx` (`product_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_skus_info`
--

DROP TABLE IF EXISTS `css_skus_info`;
CREATE TABLE IF NOT EXISTS `css_skus_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) NOT NULL,
  `master_catalog_item` varchar(100) NOT NULL,
  `sub_skus` text,
  `model_number` varchar(200) DEFAULT NULL,
  `product_type` varchar(20) DEFAULT NULL,
  `price_type` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `labor_price` decimal(10,2) DEFAULT NULL,
  `usage_code` varchar(100) DEFAULT NULL,
  `size` varchar(100) DEFAULT NULL,
  `ring_size` varchar(100) DEFAULT NULL,
  `product_part` varchar(100) DEFAULT NULL,
  `earrings` varchar(100) DEFAULT NULL,
  `weight` varchar(100) DEFAULT NULL,
  `style` varchar(100) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  `metarial` varchar(100) DEFAULT NULL,
  `length` varchar(100) DEFAULT NULL,
  `gauge` varchar(100) DEFAULT NULL,
  `cstandard` varchar(100) DEFAULT NULL,
  `thumbnail` text,
  `boms` text,
  `can_letter` tinyint(1) DEFAULT '0',
  `letter_info` varchar(100) DEFAULT NULL,
  `certificates` text,
  `spec_name` varchar(255) DEFAULT NULL,
  `sku_spec` text,
  `sku_type` tinyint(1) DEFAULT '0' COMMENT '0:基础SKU,1:虚拟SKU',
  `special_type` tinyint(1) DEFAULT '0' COMMENT '1:charme,2:presale',
  `has_main_material` tinyint(1) DEFAULT '0',
  `diamond_set` varchar(100) DEFAULT NULL,
  `final_boms` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_spec`
--

DROP TABLE IF EXISTS `css_spec`;
CREATE TABLE IF NOT EXISTS `css_spec` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_spp_rules`
--

DROP TABLE IF EXISTS `css_spp_rules`;
CREATE TABLE IF NOT EXISTS `css_spp_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rule_type` tinyint(1) NOT NULL COMMENT '1按品牌系列，2按用途，3按材质，4按指定款号',
  `step_o` varchar(100) DEFAULT NULL,
  `include_style_number` text COMMENT 'type为4时生效',
  `is_child` tinyint(1) DEFAULT '0' COMMENT '针对type为1有效',
  `step_t` varchar(100) DEFAULT NULL COMMENT '针对type为1有效',
  `image` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_spp_rules_relate`
--

DROP TABLE IF EXISTS `css_spp_rules_relate`;
CREATE TABLE IF NOT EXISTS `css_spp_rules_relate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) NOT NULL,
  `rule_type` tinyint(1) NOT NULL,
  `rule_data` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `css_virtual_categories_info`
--

DROP TABLE IF EXISTS `css_virtual_categories_info`;
CREATE TABLE IF NOT EXISTS `css_virtual_categories_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) NOT NULL,
  `product_type` text,
  `exclude` text,
  `extra` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rule_id` (`rule_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的结构 `wechat_access_token`
--

DROP TABLE IF EXISTS `wechat_access_token`;
CREATE TABLE IF NOT EXISTS `wechat_access_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(100) NOT NULL COMMENT '谁的Token',
  `token` text NOT NULL COMMENT 'AccessToken值',
  `expired_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=2 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
