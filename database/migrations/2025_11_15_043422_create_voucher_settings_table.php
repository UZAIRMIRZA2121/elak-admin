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
        Schema::create('voucher_settings', function (Blueprint $table) {
            $table->id();
            $table->text("validity_period")->nullable();
            $table->text("specific_days_of_week")->nullable();
            $table->text("holidays_occasions")->nullable();
            $table->text("custom_blackout_dates")->nullable();
            $table->string("age_restriction")->nullable();
            $table->text("group_size_requirement")->nullable();
            $table->text("usage_limit_per_user")->nullable();
            $table->text("usage_limit_per_store")->nullable();
            $table->string("offer_validity_after_purchase")->nullable();
            $table->text("general_restrictions")->nullable();
            $table->integer("voucher_id")->nullable();
            $table->string("status")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_settings');
    }
};
