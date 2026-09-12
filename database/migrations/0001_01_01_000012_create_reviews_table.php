<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index('idx_reviews_user')
                ->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('product_id')
                ->constrained('products')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('order_item_id')->nullable()->unique('uq_reviews_order_item')
                ->constrained('order_items')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->string('status', 20)->default('pending');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['product_id', 'status', 'created_at'], 'idx_reviews_product_status_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
