<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSortToPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $sql='alter table `page` Add column `sort` int not null default 1 AFTER `published_time`;';
        Schema::getConnection()->statement($sql);
        $sql='update `page` set `sort` = -2 where `id` =1;';
        Schema::getConnection()->statement($sql);
        $sql='update `page` set `sort` = -1 where `id` =2;';
        Schema::getConnection()->statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
