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
        Schema::create('color_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('color_theme_id');

            $table->string('color_name');
            $table->string('color_code')->nullable();
            $table->string('color_gradient')->nullable();
            $table->string('color_type')->nullable();
            $table->tinyInteger('status')->default(1);

            $table->timestamps();

         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_codes');
    }
};
