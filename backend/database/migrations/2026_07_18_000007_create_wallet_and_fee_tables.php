<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // cấu hình phí sàn (1 dòng, admin chỉnh) — % và tiền cố định
        Schema::create('fee_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('payment_fee_rate', 5, 2)->default(0); // %
            $table->decimal('tech_fee_rate', 5, 2)->default(0);    // %
            $table->decimal('infra_fee_amount', 15, 2)->default(0); // tiền/đơn
            $table->timestamps();
        });

        // bóc phí chốt theo từng shop_order (để đối soát)
        Schema::create('shop_order_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_order_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('commission', 15, 2)->default(0);
            $table->decimal('payment_fee', 15, 2)->default(0);
            $table->decimal('tech_fee', 15, 2)->default(0);
            $table->decimal('infra_fee', 15, 2)->default(0);
            $table->decimal('platform_total', 15, 2)->default(0);
            $table->decimal('seller_earning', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('seller_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->unique()->constrained('shops')->onDelete('cascade');
            $table->decimal('available', 15, 2)->default(0); // rút được
            $table->decimal('pending', 15, 2)->default(0);   // đang giữ (escrow)
            $table->timestamps();
        });

        // sổ cái bất biến — số dư = tổng bút toán
        Schema::create('wallet_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->string('type'); // sale_earning / commission / payout / refund / adjust
            $table->decimal('amount', 15, 2); // có dấu (+/-)
            $table->string('ref_type')->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->decimal('balance_after', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_ledger');
        Schema::dropIfExists('seller_wallets');
        Schema::dropIfExists('shop_order_fees');
        Schema::dropIfExists('fee_settings');
    }
};
