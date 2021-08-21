<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->float('discount_amount')->after('is_discounted')->nullable();
            $table->string('discount_type')->after('discount_amount')->nullable();
            $table->float('delivery_charge')->after('discounted_total')->nullable();
            $table->float('grand_total')->after('delivery_charge')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_amount');
            $table->dropColumn('delivery_charge');
            $table->dropColumn('grand_total');
        });
    }
}
