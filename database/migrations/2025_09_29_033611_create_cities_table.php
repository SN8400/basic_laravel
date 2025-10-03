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
        Schema::connection('sqlsrv2')->create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('province_id')->nullable();
            $table->string('th_name', 100)->nullable();
            $table->string('en_name', 100)->nullable();
            $table->dateTime('created')->nullable();
            $table->dateTime('modified')->nullable();

            $table->primary(['id'], 'PK_cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlsrv2')->dropIfExists('cities');
    }
};
