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
        Schema::table('orders', function (Blueprint $table) {

            $table->float('discount_amount')
                ->default(0)
                ->after('offer_type'); // ✅ After offer_type

        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn('discount_amount');

        });
    }
};
