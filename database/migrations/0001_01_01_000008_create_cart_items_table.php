<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')
                ->constrained('carts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->index('idx_cart_items_product')
                ->constrained('products')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('quantity', 12, 3)->default(1);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['cart_id', 'product_id'], 'uq_cart_items_cart_product');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
