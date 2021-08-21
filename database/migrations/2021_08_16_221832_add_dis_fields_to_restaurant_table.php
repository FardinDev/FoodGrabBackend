<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisFieldsToRestaurantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('discount_type')->after('is_open')->nullable();
            $table->float('discount_amount')->after('discount_type')->nullable();
            $table->float('discount_cap')->after('discount_amount')->nullable();
        });
        Schema::table('items', function (Blueprint $table) {
            $table->string('discount_type')->after('is_available')->nullable();
            $table->float('discount_amount')->after('discount_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_amount');
            $table->dropColumn('discount_cap');
        });
        
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_amount');
        });
    }
}
