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
        Schema::connection('sqlsrv2')->create('crops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->text('details')->nullable();
            $table->string('sap_code', 20)->nullable();
            $table->date('startdate')->nullable();
            $table->date('enddate')->nullable();
            $table->string('linkurl', 50)->nullable();
            $table->integer('createdBy')->nullable();
            $table->integer('modifiedBy')->nullable();
            $table->dateTime('created')->nullable();
            $table->dateTime('modified')->nullable();
            $table->float('max_per_day', 0, 0)->nullable();

            $table->primary(['id'], 'PK_crops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlsrv2')->dropIfExists('crops');
    }
};
