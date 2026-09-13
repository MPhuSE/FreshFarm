<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung tạm thời để middleware CheckRole (TV2 dùng cho Admin Product)
 * có thể hoạt động ngay. TV1 khi làm module Auth chính thức cần rà soát
 * lại migration này (có thể merge vào migration users gốc hoặc giữ riêng).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('customer')->after('email');
            $table->string('status', 20)->default('active')->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status']);
        });
    }
};