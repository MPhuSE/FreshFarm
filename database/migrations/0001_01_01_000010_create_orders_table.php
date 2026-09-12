<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 32)->unique('uq_orders_order_code');
            $table->foreignId('user_id')
                ->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('coupon_id')->nullable()
                ->constrained('coupons')->onUpdate('cascade')->onDelete('set null');
            $table->string('status', 20)->default('pending');
            $table->string('payment_method', 30)->default('cod');
            $table->string('payment_status', 20)->default('unpaid');
            $table->string('recipient_name', 120);
            $table->string('phone', 20);
            $table->string('shipping_address', 500);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('carrier', 100)->nullable();
            $table->string('tracking_code', 100)->nullable()->index('idx_orders_tracking_code');
            $table->string('note', 500)->nullable();
            $table->dateTime('placed_at')->useCurrent();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['user_id', 'placed_at'], 'idx_orders_user_placed');
            $table->index(['status', 'placed_at'], 'idx_orders_status_placed');
            $table->index('coupon_id', 'idx_orders_coupon');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
