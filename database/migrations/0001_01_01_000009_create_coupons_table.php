<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique('uq_coupons_code');
            $table->string('type', 20);
            $table->decimal('value', 15, 2);
            $table->decimal('min_order_amount', 15, 2)->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('status', 20)->default('active');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['status', 'starts_at', 'ends_at'], 'idx_coupons_status_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
