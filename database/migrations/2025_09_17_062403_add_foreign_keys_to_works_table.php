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
        Schema::table('works', function (Blueprint $table) {
            $table->foreign(['plan_id'], 'fk_works_plan')->references(['id'])->on('plans')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['room_id'], 'fk_works_room')->references(['id'])->on('rooms')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropForeign('fk_works_plan');
            $table->dropForeign('fk_works_room');
        });
    }
};
