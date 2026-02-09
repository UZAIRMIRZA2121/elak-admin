<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add new columns right after order_amount
            $table->float('total_order_amount')->default(0)->after('order_amount');
            $table->string('offer_type')->nullable()->after('total_order_amount');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_order_amount', 'offer_type']);
        });
    }

};
