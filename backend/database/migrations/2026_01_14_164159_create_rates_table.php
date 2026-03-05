<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('blog_id');
            $table->tinyInteger('rate'); // 1 → 5

            $table->timestamps();

            // mỗi user chỉ được đánh giá 1 lần cho 1 blog
            $table->unique(['user_id', 'blog_id']);

            // nếu có bảng users và blogs thì nên thêm khóa ngoại
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('blog_id')
                  ->references('id')->on('blogs')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
