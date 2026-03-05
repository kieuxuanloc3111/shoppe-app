<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // basic info
            $table->string('name');
            $table->decimal('price', 15, 2);

            // sale
            $table->tinyInteger('sale')->default(0); // 0: new, 1: sale
            $table->decimal('sale_price', 15, 2)->nullable();

            // info
            $table->string('company');
            $table->text('detail');

            // image (json string)
            $table->string('image'); // lưu json_encode

            // relation
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('user_id');

            $table->timestamps();

            /* ======================
                FOREIGN KEY
            ====================== */

            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('cascade');

            $table->foreign('brand_id')
                  ->references('id')->on('brands')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
