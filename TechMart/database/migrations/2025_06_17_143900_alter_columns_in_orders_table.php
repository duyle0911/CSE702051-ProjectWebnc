<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('tax_amount', 15, 2)->change();
            $table->decimal('shipping_fee', 15, 2)->change();
            $table->decimal('total_amount', 15, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->change();
            $table->decimal('tax_amount', 10, 2)->change();
            $table->decimal('shipping_fee', 10, 2)->change();
            $table->decimal('total_amount', 10, 2)->change();
        });
    }
};

