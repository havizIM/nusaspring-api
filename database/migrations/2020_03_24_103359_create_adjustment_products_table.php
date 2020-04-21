<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjustmentProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjustment_products', function (Blueprint $table) {
            $table->foreignId('adjustment_id')->references('id')->on('adjustments')->unsigned();
            $table->foreignId('product_id')->references('id')->on('products')->unsigned();
            $table->string('description');
            $table->integer('qty');
            $table->string('unit');
            $table->double('unit_price', 15, 2);
            $table->double('total', 15, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjustment_products');
    }
}
