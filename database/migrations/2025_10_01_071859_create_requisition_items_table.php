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
        Schema::create('requisition_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('requisition_id');
            $table->integer('stock_id');
            $table->float('qty_requested', 0, 0);
            $table->float('qty_approved', 0, 0)->nullable();
            $table->text('remark')->nullable();

            $table->primary(['id'], 'PK_requisition_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisition_items');
    }
};
