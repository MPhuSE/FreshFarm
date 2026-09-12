<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->index('idx_order_items_order')
                ->constrained('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->index('idx_order_items_product')
                ->constrained('products')->onUpdate('cascade')->onDelete('set null');
            $table->string('product_name', 180);
            $table->string('unit', 30);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('quantity', 12, 3);
            $table->decimal('line_total', 15, 2);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
