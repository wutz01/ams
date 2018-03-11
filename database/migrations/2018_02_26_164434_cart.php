<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Cart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart', function (Blueprint $table) {
          $table->increments('id');
          $table->string('guestId');
          $table->integer('userId')->unsigned();
          $table->string('status')->default('ACTIVE');
          $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('cartId')->unsigned();
          $table->foreign('cartId')->references('id')->on('cart');
          $table->integer('productId')->unsigned();
          $table->float('reservePrice', 8, 2);
          $table->float('finalPrice', 8, 2)->default(0);
          $table->integer('quantity')->default(1);
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
        Schema::table('cart_items', function (Blueprint $table) {
          $table->dropForeign(['cartId']);
        });
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('cart');
    }
}
