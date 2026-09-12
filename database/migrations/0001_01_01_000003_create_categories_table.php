<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()
                ->constrained('categories')->onUpdate('cascade')->onDelete('set null');
            $table->string('name', 120);
            $table->string('slug', 160)->unique('uq_categories_slug');
            $table->text('description')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->string('status', 20)->default('active');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->dateTime('deleted_at')->nullable();

            $table->index(['parent_id', 'status'], 'idx_categories_parent_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
