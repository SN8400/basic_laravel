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
        Schema::create('sup_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('crop_id');
            $table->integer('broker_id');
            $table->integer('chemical_id');
            $table->float('value', 0, 0);
            $table->integer('unit_id');

            $table->primary(['id'], 'PK_sup_stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sup_stocks');
    }
};
