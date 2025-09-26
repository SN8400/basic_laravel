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
        Schema::table('employee_external_maps', function (Blueprint $table) {
            $table->foreign(['internal_emp_id'], 'fk_emp_map_emp')->references(['id'])->on('employees')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_external_maps', function (Blueprint $table) {
            $table->dropForeign('fk_emp_map_emp');
        });
    }
};
