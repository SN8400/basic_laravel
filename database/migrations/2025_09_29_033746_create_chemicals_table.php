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
        Schema::connection('sqlsrv2')->create('chemicals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->nullable();
            $table->string('name', 100)->nullable();
            $table->text('details')->nullable();
            $table->dateTime('created')->nullable();
            $table->dateTime('modified')->nullable();
            $table->string('formula_code', 50)->nullable();
            $table->integer('standard_code_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->float('rate_per_land', 0, 0)->nullable();
            $table->integer('bigunit_id')->nullable();
            $table->float('package_per_bigunit', 0, 0)->nullable();
            $table->string('ctype', 10)->nullable();

            $table->primary(['id'], 'PK_chemicals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlsrv2')->dropIfExists('chemicals');
    }
};
