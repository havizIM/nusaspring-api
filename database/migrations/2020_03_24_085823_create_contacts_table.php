<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('contact_name')->index('contact_name');
            $table->enum('type', ['Customer', 'Supplier']);
            $table->string('pic')->nullable();
            $table->string('phone', 12)->nullable();
            $table->string('fax', 12)->nullable();
            $table->string('handphone', 12)->nullable();
            $table->string('email', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('npwp', 20)->nullable();
            $table->text('memo')->nullable();
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
        Schema::dropIfExists('contacts');
    }
}
