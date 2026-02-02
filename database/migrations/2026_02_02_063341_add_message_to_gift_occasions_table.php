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
        Schema::table('gift_occasions', function (Blueprint $table) {
            $table->text('title')->nullable()->change();
            $table->text('message')->nullable()->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gift_occasions', function (Blueprint $table) {
            $table->string('title', 100)->nullable()->change();
            $table->dropColumn('message');
        });
    }
};
