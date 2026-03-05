<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('blog_id');
            $table->unsignedBigInteger('user_id');

            $table->unsignedBigInteger('parent_id')->nullable(); // reply
            $table->tinyInteger('level')->default(0); // 0 cha | 1 con

            $table->text('content');

            $table->string('user_name');
            $table->string('user_avatar')->nullable();

            $table->timestamps();

            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
