<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selling_products', function (Blueprint $table) {
            $table->foreignId('selling_id')->references('id')->on('sellings')->unsigned();
            $table->foreignId('product_id')->references('id')->on('products')->unsigned();
            $table->string('description');
            $table->integer('qty');
            $table->string('unit');
            $table->double('unit_price', 15, 2);
            $table->enum('ppn', ['Y', 'N'])->default('N');
            $table->double('discount_percent', 15, 2)->nullable();
            $table->double('discount_amount', 15, 2)->nullable();
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
        Schema::dropIfExists('selling_products');
    }
}
