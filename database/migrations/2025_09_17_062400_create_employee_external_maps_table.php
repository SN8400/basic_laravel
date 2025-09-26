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
        Schema::create('employee_external_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source', 64);
            $table->string('external_emp_id', 128);
            $table->integer('internal_emp_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_external_maps');
    }
};
