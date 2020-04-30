<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->index('purchase_id');
            $table->foreignId('purchase_id')->references('id')->on('purchases')->unsigned();

            $table->index('product_id');
            $table->foreignId('product_id')->references('id')->on('products')->unsigned();

            $table->string('description');
            $table->double('qty', 15, 2);
            $table->string('unit')->nullable();
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
        Schema::dropIfExists('purchase_products');
    }
}
