<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_update_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sys_name', 50);
            $table->string('sys_status', 10);
            $table->string('created_at', 8);
            $table->string('updated_at', 8);

            $table->primary(['id'], 'PK_sys_update_logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_update_logs');
    }
};
