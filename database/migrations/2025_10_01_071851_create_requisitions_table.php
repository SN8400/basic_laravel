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
        Schema::create('requisitions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('crop_id');
            $table->integer('broker_id');
            $table->integer('stock_id');
            $table->string('inventory_code', 50);
            $table->string('inventory_type', 50);
            $table->string('inventory_status', 50);
            $table->string('request_date', 8);
            $table->integer('request_by');
            $table->string('approved_date', 8);
            $table->integer('approved_by');
            $table->string('created', 8)->nullable();
            $table->string('modified', 8)->nullable();

            $table->primary(['id'], 'PK_requisition');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisitions');
    }
};
