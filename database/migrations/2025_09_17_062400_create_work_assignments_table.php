<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('work_id');
            $table->integer('room_id');
            $table->string('pos_code', 64);
            $table->integer('emp_id');
            $table->enum('current_status', ['([current_status]=\'OFF_POSITION\' OR [current_status]=\'ON_BREAK\' OR [current_status]=\'ON_DUTY\' OR [current_status]=\'UNKNOWN']);
            $table->dateTime('current_status_since')->nullable();
            $table->integer('from_plan_item_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_assignments');
    }
};
