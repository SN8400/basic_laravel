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
        Schema::connection('sqlsrv2')->create('brokers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->nullable();
            $table->string('init', 50)->nullable();
            $table->string('fname', 100)->nullable();
            $table->string('lname', 100)->nullable();
            $table->string('citizenid', 100)->nullable();
            $table->string('address1', 200)->nullable();
            $table->string('address2', 200)->nullable();
            $table->string('address3', 200)->nullable();
            $table->string('sub_cities', 200)->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('province_id')->nullable();
            $table->string('loc', 50)->nullable();
            $table->string('broker_color', 20)->nullable();
            $table->integer('createdBy')->nullable();
            $table->integer('modifiedBy')->nullable();
            $table->dateTime('created')->nullable();
            $table->dateTime('modified')->nullable();

            $table->primary(['id'], 'PK_brokers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlsrv2')->dropIfExists('brokers');
    }
};
