<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cost_details', function (Blueprint $table) {
            
            $table->index('cost_id');
            $table->foreignId('cost_id')->references('id')->on('costs')->unsigned();

            $table->string('description');
            $table->enum('ppn', ['Y', 'N'])->default('N');
            $table->double('amount', 15, 2);
            $table->double('discount_percent', 15, 2)->nullable();
            $table->double('discount_amount', 15, 2)->nullable();
            $table->text('attachment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cost_details');
    }
}
