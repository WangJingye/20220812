<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "CREATE TABLE `crm_customers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `wechat_user_id` int(11) NOT NULL COMMENT '关联wechat_id',
  `customer_id` varchar(50) NOT NULL DEFAULT '' COMMENT 'Customer_id',
  `family_name` varchar(50) NOT NULL DEFAULT '''''' COMMENT '姓',
  `first_name` varchar(100) NOT NULL DEFAULT '''''' COMMENT '名',
  `gender` varchar(11) NOT NULL DEFAULT '''''' COMMENT '性别 M男',
  `date_of_birth` varchar(50) DEFAULT '' COMMENT '生日',
  `email` varchar(50) DEFAULT NULL COMMENT 'E-mail',
  `residence_country` varchar(100) NOT NULL DEFAULT '''''' COMMENT '居住地',
  `mobile_country_code` tinyint(4) DEFAULT NULL COMMENT '国家代码',
  `mobile_number` varchar(25) NOT NULL DEFAULT '' COMMENT '手机号码',
  `member_class` varchar(4) DEFAULT NULL COMMENT '等级',
  `stateCode` varchar(4) DEFAULT 'P' COMMENT 'P:待验证的周友账号 V:有效的周友账号',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
";
        Schema::getConnection()->statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crm_customers');
    }
}
