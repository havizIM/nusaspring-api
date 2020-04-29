<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellingReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selling_returns', function (Blueprint $table) {
            $table->id();

            $table->index('contact_id');
            $table->foreignId('contact_id')->references('id')->on('contacts')->unsigned();

            $table->index('selling_id');
            $table->foreignId('selling_id')->nullable()->references('id')->on('sellings');

            $table->string('return_number', 20)->index('return_number');
            $table->string('reference_number', 20)->nullable();
            $table->text('message')->nullable();
            $table->text('memo')->nullable();
            $table->text('attachment')->nullable();
            $table->double('total_ppn', 15, 2)->nullable();
            $table->date('date');
            $table->timestamps();
            $table->softDeletes(); // deleted_at
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selling_returns');
    }
}
