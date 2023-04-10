CREATE TABLE `oms_order_comment` (
`order_sn` varchar(50) COLLATE utf8mb4_bin NOT NULL,
`content` text COLLATE utf8mb4_bin COMMENT '评价内容',
`score_p` tinyint(2) DEFAULT 0 not null COMMENT '商品分数',
`score_cs` tinyint(2) DEFAULT 0 not null COMMENT '客服分数',
`score_l` tinyint(2) DEFAULT 0 not null COMMENT '物流分数',
`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`order_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER table `oms_order_main` add column is_comment tinyint(1) default 0 not null COMMENT '是否评论';

CREATE TABLE `oms_order_return_apply` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`order_sn` varchar(50) COLLATE utf8mb4_bin NOT NULL,
`content` text COLLATE utf8mb4_bin,
`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单退货申请状态 0 未处理, 1 同意, 2 拒绝',
`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
KEY `order_sn` (`order_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER table `oms_order_main` add column is_apply_return tinyint(1) default 0 not null COMMENT '是否申请退货';
ALTER table `oms_order_main` add column is_allow_return tinyint(1) default 0 not null COMMENT '是否允许退货退款';
ALTER table `oms_order_main` add column is_return_wms tinyint(1) default 0 not null COMMENT '是否已经退回仓库';
