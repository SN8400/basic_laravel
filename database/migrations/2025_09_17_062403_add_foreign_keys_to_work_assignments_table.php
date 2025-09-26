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
        Schema::table('work_assignments', function (Blueprint $table) {
            $table->foreign(['emp_id'], 'fk_work_assignments_emp')->references(['id'])->on('employees')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['from_plan_item_id'], 'fk_work_assignments_plan_item')->references(['id'])->on('plan_items')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['work_id'], 'fk_work_assignments_work')->references(['id'])->on('works')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_assignments', function (Blueprint $table) {
            $table->dropForeign('fk_work_assignments_emp');
            $table->dropForeign('fk_work_assignments_plan_item');
            $table->dropForeign('fk_work_assignments_work');
        });
    }
};
