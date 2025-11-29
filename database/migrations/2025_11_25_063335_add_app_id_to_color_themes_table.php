<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('color_themes', function (Blueprint $table) {
            $table->unsignedBigInteger('app_id')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('color_themes', function (Blueprint $table) {
            $table->dropColumn('app_id');
        });
    }

};