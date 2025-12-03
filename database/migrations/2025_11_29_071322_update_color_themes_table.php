<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('color_themes', function (Blueprint $table) {
            Schema::table('color_themes', function (Blueprint $table) {
                $table->dropColumn(['color_name', 'color_code', 'color_gradient', 'color_type', 'app_id']);

                $table->string('name')->after('id');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('color_themes', function (Blueprint $table) {
            $table->string('color_name')->nullable();
            $table->string('color_code')->nullable();
            $table->string('color_gradient')->nullable();
            $table->string('color_type')->nullable();
            $table->unsignedBigInteger('app_id')->nullable();

            $table->dropColumn(['name', 'start_date', 'end_date']);
        });
    }
};
