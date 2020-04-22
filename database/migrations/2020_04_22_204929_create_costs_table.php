<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->string('cost_number', 20);
            $table->string('to', 50);
            $table->enum('type', ['Cash', 'Cek/Giro', 'Transfer', 'Kartu Kredit']);
            $table->date('date');
            $table->text('message')->nullable();
            $table->text('memo')->nullable();
            $table->text('attachment')->nullable();
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
        Schema::dropIfExists('costs');
    }
}
