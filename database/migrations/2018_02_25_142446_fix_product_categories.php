<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixProductCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::dropIfExists('category_product');
      Schema::create('categories_product', function (Blueprint $table) {
          $table->integer('category_id')->unsigned()->nullable();
          $table->foreign('category_id')->references('id')
            ->on('categories')->onDelete('cascade');
          $table->integer('product_id')->unsigned()->nullable();
          $table->foreign('product_id')->references('id')
            ->on('products')->onDelete('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('categories_product');
      Schema::create('category_product', function (Blueprint $table) {
          $table->integer('product_id');
          $table->integer('category_id');
      });
    }
}
