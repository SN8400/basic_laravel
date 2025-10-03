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
        Schema::connection('sqlsrv2')->create('input_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('tradename', 100)->nullable();
            $table->string('common_name', 100)->nullable();
            $table->float('size', 0, 0)->nullable();
            $table->integer('unit_id')->nullable();
            $table->text('pur_of_use')->nullable();
            $table->string('RM_Group', 50)->nullable();
            $table->dateTime('created')->nullable();
            $table->dateTime('modified')->nullable();

            $table->primary(['id'], 'PK_input_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlsrv2')->dropIfExists('input_items');
    }
};
