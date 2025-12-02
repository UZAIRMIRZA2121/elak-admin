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
        Schema::create('app_themes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_id');
            $table->unsignedBigInteger('theme_id');
            $table->timestamps();

            // // Optional: add foreign keys
            // $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            // $table->foreign('theme_id')->references('id')->on('color_themes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_themes');
    }
};
