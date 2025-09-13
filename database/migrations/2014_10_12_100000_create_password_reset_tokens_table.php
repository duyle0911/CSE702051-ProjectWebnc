<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // Email: khóa chính, đồng thời đánh index để tìm nhanh hơn
            $table->string('email')->primary();

            // Token reset mật khẩu
            $table->string('token', 255);

            // Thời gian tạo token
            $table->timestamp('created_at')->nullable();

            // Thời gian hết hạn token (tùy chọn thêm)
            $table->timestamp('expires_at')->nullable();

            // Index phụ để tìm nhanh theo token
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
