<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');

            // snapshot địa chỉ nhận (không phụ thuộc sổ địa chỉ về sau)
            $table->string('receiver_name');
            $table->string('receiver_phone', 20);
            $table->string('receiver_address', 500);

            $table->decimal('grand_total', 15, 2);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // cod / vnpay (P2)
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->enum('status', [
                'pending', 'confirmed', 'shipping', 'delivered', 'completed', 'cancelled',
            ])->default('pending');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_order_id')->constrained()->onDelete('cascade');
            // product/variant có thể bị sửa/xóa sau — snapshot name+price giữ lịch sử đơn
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('name');                    // snapshot tên
            $table->decimal('unit_price', 15, 2);      // snapshot giá
            $table->unsignedInteger('qty');
            $table->decimal('line_total', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('shop_orders');
        Schema::dropIfExists('orders');
    }
};
