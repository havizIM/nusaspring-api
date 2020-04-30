<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockOpnameProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_opname_products', function (Blueprint $table) {
            $table->index('stock_opname_id');
            $table->foreignId('stock_opname_id')->references('id')->on('stock_opnames')->unsigned();

            $table->index('product_id');
            $table->foreignId('product_id')->references('id')->on('products')->unsigned();

            $table->string('description');
            $table->double('unit_price', 15, 2);
            $table->string('unit')->nullable();
            $table->double('system_qty', 15, 2);
            $table->double('actual_qty', 15, 2);
            $table->double('system_total', 15, 2);
            $table->double('actual_total', 15, 2);
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_opname_products');
    }
}
