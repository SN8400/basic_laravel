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
        Schema::connection('sqlsrv2')->create('plan_schedule_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('plan_schedule_id')->nullable();
            $table->integer('chemical_id')->nullable();
            $table->float('value', 0, 0)->nullable();
            $table->integer('unit_id')->nullable();
            $table->float('p_value', 0, 0)->nullable();
            $table->integer('p_unit_id')->nullable();
            $table->dateTime('created')->nullable();
            $table->dateTime('modified')->nullable();
            $table->string('name', 100)->nullable();
            $table->tinyInteger('rate')->nullable();
            $table->string('ctype', 10)->nullable();
            $table->integer('set_group')->nullable();

            $table->primary(['id'], 'PK_plan_schedule_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlsrv2')->dropIfExists('plan_schedule_details');
    }
};
