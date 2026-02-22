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
        Schema::table('carts', function (Blueprint $table) {

            // After price column
            $table->float('total_price')->nullable()->after('price');

            $table->string('offer_type')->nullable()->after('total_price');
            // Example:
            // direct discount
            // cashback

            $table->float('discount_amount')->nullable()->after('offer_type');

        });
    }

    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {

            $table->dropColumn([
                'total_price',
                'offer_type',
                'discount_amount'
            ]);

        });
    }
};
