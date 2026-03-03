<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('voucher_usage_term_and_conditions')->nullable()->after('gift_details');
            $table->json('voucher_term_and_conditions')->nullable()->after('voucher_usage_term_and_conditions');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'voucher_usage_term_and_conditions',
                'voucher_term_and_conditions'
            ]);
        });
    }
};