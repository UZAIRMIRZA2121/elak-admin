<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('commission_amount', 24, 8)->default(0)->after('discount_amount');
            $table->decimal('commission', 24, 8)->default(0)->after('commission_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['commission_amount', 'commission']);
        });
    }
};