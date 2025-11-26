<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::table('app_banners', function (Blueprint $table) {
        $table->unsignedBigInteger('app_id')->after('id'); // add app_id
        $table->dropColumn('app_owner_name');             // remove app_owner_name
    });
}

public function down()
{
    Schema::table('app_banners', function (Blueprint $table) {
        $table->string('app_owner_name')->after('id');
        $table->dropColumn('app_id');
    });
}

};