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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tokenable_type');
            $table->integer('tokenable_id');
            $table->string('name');
            $table->string('token', 64)->unique('UQ__personal__CA90DA7A237E44E5');
            $table->text('abilities')->nullable();
            $table->dateTime('last_used_at', 7)->nullable();
            $table->dateTime('expires_at', 7)->nullable();
            $table->dateTime('created_at', 7)->default('sysdatetime()');
            $table->dateTime('updated_at', 7)->default('sysdatetime()');

            $table->index(['tokenable_type', 'tokenable_id'], 'IX_pat_tokenable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
