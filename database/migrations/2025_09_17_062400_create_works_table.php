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
        Schema::create('works', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('room_id');
            $table->integer('plan_id')->nullable();
            $table->date('work_date');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->enum('work_status', ['([work_status]=\'Cancelled\' OR [work_status]=\'Closed\' OR [work_status]=\'Running']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
