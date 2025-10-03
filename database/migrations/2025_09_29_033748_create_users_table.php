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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('username', 100)->unique('UQ__users__F3DBC57291B904F4');
            $table->string('email')->unique('UQ__users__AB6E6164CDB023EA');
            $table->string('password');
            $table->rememberToken();
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at', 7)->default('sysdatetime()');
            $table->dateTime('updated_at', 7)->default('sysdatetime()');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
