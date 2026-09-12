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
            $table->foreignId('category_id')
                ->constrained('categories')->onUpdate('cascade')->onDelete('restrict');
            $table->string('sku', 64)->unique('uq_products_sku');
            $table->string('name', 180);
            $table->string('slug', 220)->unique('uq_products_slug');
            $table->string('unit', 30);
            $table->string('origin', 150)->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('compare_at_price', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->boolean('featured')->default(false);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();

            $table->index(['category_id', 'status'], 'idx_products_category_status');
            $table->index('featured', 'idx_products_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
