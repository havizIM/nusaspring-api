<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->references('id')->on('contacts')->unsigned();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('selling_number', 20);
            $table->string('reference_number', 20)->nullable();
            $table->text('message')->nullable();
            $table->text('memo')->nullable();
            $table->text('attachment')->nullable();
            $table->double('total_ppn', 15, 2)->nullable();
            $table->date('date');
            $table->date('due_date')->nullable();
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
        Schema::dropIfExists('sellings');
    }
}
