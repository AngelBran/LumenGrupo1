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
            $table->id();
            $table->string('names', 150);
            $table->string('lastnames', 200);
            $table->string('username', 200)->unique();
            $table->string('email', 255)->unique();
            $table->date('birthday');
            $table->integer('phone')->unique();
            $table->string('password', 255);
            $table->string('code', 150);
            $table->boolean('register_status');

            $table->timestamps();
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
