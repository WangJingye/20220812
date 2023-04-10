# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.24-log)
# Database: chowsangsang
# Generation Time: 2019-09-12 01:59:54 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table browse_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `browse_history`;

CREATE TABLE `browse_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `open_id` varchar(50) DEFAULT NULL COMMENT '用户id',
  `product_id` int(11) DEFAULT NULL COMMENT '产品id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table coupon_tag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `coupon_tag`;



# Dump of table favorite_records
# ------------------------------------------------------------

DROP TABLE IF EXISTS `favorite_records`;

CREATE TABLE `favorite_records` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `open_id` varchar(50) DEFAULT NULL COMMENT '用户ID',
  `product_id` int(11) DEFAULT NULL COMMENT '商品id',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`id`, `migration`, `batch`)
VALUES
	(1,'2014_10_12_000000_create_users_table',1),
	(2,'2014_10_12_100000_create_password_resets_table',1);

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table shipping_address
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shipping_address`;

CREATE TABLE `shipping_address` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `open_id` varchar(50) NOT NULL DEFAULT '' COMMENT '用户身份标识',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '收件人姓名',
  `phone` char(11) NOT NULL DEFAULT '' COMMENT '收货人手机号',
  `province` varchar(50) NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(50) NOT NULL DEFAULT '' COMMENT '市',
  `district` varchar(50) NOT NULL DEFAULT '' COMMENT '区/县',
  `full_address` varchar(1000) NOT NULL DEFAULT '' COMMENT '详细地址',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `shipping_address` WRITE;
/*!40000 ALTER TABLE `shipping_address` DISABLE KEYS */;

INSERT INTO `shipping_address` (`id`, `open_id`, `name`, `phone`, `province`, `city`, `district`, `full_address`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,'0216HYtf0aOeWt1TIJvf0uLNtf06HYtp','李壮壮','18210261140','上海市','上海市','徐汇区','平福路188号聚鑫园2号楼','2019-09-03 14:01:49','2019-09-03 14:01:49',NULL);

/*!40000 ALTER TABLE `shipping_address` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `users_email_unique` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`)
VALUES
	(1,'user01','user01@163.com','$2y$10$yku4ysf.9K.5EH1IO/2MlOwgMPj4zj8XfaL.qOtcgoRmb4vlewZZK','N37AfFRO4Evo8Pwysd2NnbkzyyrKyssJe3H4otQDVglhKi8EWtBZfMDYlT2S','2019-05-23 01:54:06','2019-05-23 01:54:06'),
	(2,'user02','user02@163.com','$2y$10$vt4GzM2ttlaSGOkKmG1oQuowHE13pjhBiUInuTdG2.xPN3WNjjjvy',NULL,'2019-05-23 01:54:40','2019-05-23 01:54:40');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wechat_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wechat_users`;

CREATE TABLE `wechat_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `unionid` varchar(50) DEFAULT '''''' COMMENT '用户unionid',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT '用户openid',
  `session_key` varchar(100) DEFAULT '''''' COMMENT '会话Key',
  `nickName` varchar(255) DEFAULT '''''' COMMENT '微信昵称',
  `avatarUrl` varchar(255) NOT NULL COMMENT '头像',
  `gender` tinyint(5) DEFAULT '0' COMMENT '性别 0未知 1男 2女',
  `phone` varchar(11) DEFAULT '''''' COMMENT '授权手机号',
  `country` varchar(100) DEFAULT '''''' COMMENT '国家',
  `province` varchar(100) NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(100) NOT NULL DEFAULT '' COMMENT '城市',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `wechat_users` WRITE;
/*!40000 ALTER TABLE `wechat_users` DISABLE KEYS */;

INSERT INTO `wechat_users` (`id`, `unionid`, `openid`, `session_key`, `nickName`, `avatarUrl`, `gender`, `phone`, `country`, `province`, `city`, `created_at`, `updated_at`, `first_name`, `last_name`)
VALUES
	(2,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','KHUl6GP39v69ofWCx6lJgw==','巴黎下着雨','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'13890098118','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 16:08:19','Ji','Hia'),
	(3,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'18210261140','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30','Li','Meisu'),
	(4,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'18029208525','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30','Su','ruao'),
	(5,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'18210261140','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30','He','jiasa'),
	(6,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'18356715901','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30','Pi','peng'),
	(7,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'18210261140','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(8,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'18511837891','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(9,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'18655573521','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(10,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'17717112165','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(11,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'\'\'','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(12,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'\'\'','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(13,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'\'\'','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(14,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'\'\'','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(15,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'\'\'','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(16,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'\'\'','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(17,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'\'\'','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30','Kawi','TT'),
	(18,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'\'\'','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL),
	(19,'\'\'','oRT0R5dACHgsFjCnBOAvT-vsd5WM','omwM4GdM7X6R6x56URXPxw==','护卫','https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKj37zricPlTUq8IQeHeB6icOvW3W5anDERDibhPmfk4DLsibwSqNrLXvosKhPbbfaZN8PSPciaU9d6yGA/132',1,'18210261140','法国','巴黎','','2019-09-04 14:18:46','2019-09-04 14:27:30',NULL,NULL);

/*!40000 ALTER TABLE `wechat_users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
