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
        Schema::create('sup_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stock_id');
            $table->string('inventory_type', 50);
            $table->float('amount', 0, 0);
            $table->integer('unit_id');
            $table->text('remark')->nullable();

            $table->primary(['id'], 'PK_sup_inventory');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sup_inventories');
    }
};
