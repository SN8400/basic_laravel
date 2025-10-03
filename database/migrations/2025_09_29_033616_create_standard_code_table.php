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
        Schema::connection('sqlsrv2')->create('standard_code', function (Blueprint $table) {
            $table->increments('id');
            $table->string('standard_name', 100)->nullable();
            $table->text('details')->nullable();
            $table->string('chemical_type', 50)->nullable();
            $table->string('MRLs', 50)->nullable();
            $table->char('major_type', 10)->nullable();
            $table->char('type_code', 10)->nullable();
            $table->dateTime('created')->nullable();
            $table->dateTime('modified')->nullable();
            $table->integer('rate')->nullable();

            $table->primary(['id'], 'PK_standard_code2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlsrv2')->dropIfExists('standard_code');
    }
};
