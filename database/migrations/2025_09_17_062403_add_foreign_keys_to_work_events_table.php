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
        Schema::table('work_events', function (Blueprint $table) {
            $table->foreign(['assignment_id'], 'fk_work_events_assignment')->references(['id'])->on('work_assignments')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['emp_id'], 'fk_work_events_emp')->references(['id'])->on('employees')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['work_id'], 'fk_work_events_work')->references(['id'])->on('works')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_events', function (Blueprint $table) {
            $table->dropForeign('fk_work_events_assignment');
            $table->dropForeign('fk_work_events_emp');
            $table->dropForeign('fk_work_events_work');
        });
    }
};
