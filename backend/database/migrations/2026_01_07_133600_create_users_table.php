<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            $table->integer('level')->comment('1: admin, 0: member');

            $table->string('phone', 20)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('avatar')->nullable();
            $table->unsignedBigInteger('id_country')->nullable();

            $table->timestamps();

            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
