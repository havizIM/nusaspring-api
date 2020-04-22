<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selling_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->references('id')->on('contacts')->unsigned();
            $table->foreignId('selling_id')->nullable()->references('id')->on('sellings');
            $table->string('payment_number', 20);
            $table->enum('type', ['Cash', 'Cek/Giro', 'Transfer', 'Kartu Kredit']);
            $table->text('description')->nullable();
            $table->text('memo')->nullable();
            $table->text('attachment')->nullable();
            $table->date('date');
            $table->double('amount', 15, 2);
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
        Schema::dropIfExists('selling_payments');
    }
}
