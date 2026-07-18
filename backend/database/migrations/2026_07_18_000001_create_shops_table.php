<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            // ponytail: mặc định active để dev không bị chặn.
            // Thực tế sàn: mặc định 'pending', admin duyệt → 'active' (làm ở track admin).
            $table->enum('status', ['pending', 'active', 'suspended'])->default('active');
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('followers_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
