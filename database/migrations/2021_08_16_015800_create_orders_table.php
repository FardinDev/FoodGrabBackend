<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('order_number')->unique();
            $table->bigInteger('restaurant_id')->unsigned();
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->bigInteger('rider_id')->nullable()->unsigned();
            $table->foreign('rider_id')->references('id')->on('users');
            $table->bigInteger('order_status_id')->unsigned();
            $table->foreign('order_status_id')->references('id')->on('order_statuses');
            $table->bigInteger('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->text('address')->nullable()->collation('utf8_unicode_ci');
            $table->text('instruction')->nullable()->collation('utf8_unicode_ci');
            $table->float('total');
            $table->boolean('is_discounted')->default(0);
            $table->float('discounts')->nullable();
            $table->float('discounted_total')->nullable();;
            $table->timestamp('placed_at');
            $table->json('cart_details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
