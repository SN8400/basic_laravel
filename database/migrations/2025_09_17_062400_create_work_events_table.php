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
        Schema::create('work_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('work_id');
            $table->integer('assignment_id')->nullable();
            $table->integer('emp_id');
            $table->string('pos_code', 64)->nullable();
            $table->enum('status', ['([status]=\'OFF_POSITION\' OR [status]=\'ON_BREAK\' OR [status]=\'ON_DUTY']);
            $table->string('source', 64);
            $table->dateTime('event_time');
            $table->dateTime('received_at')->useCurrent();
            $table->string('idempotency_key', 128)->nullable();
            $table->text('payload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_events');
    }
};
