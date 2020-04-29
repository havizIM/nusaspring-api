<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();

            $table->index('user_id');
            $table->foreignId('user_id')->references('id')->on('users')->unsigned();
            
            $table->text('description');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->enum('color', ['primary', 'info', 'success', 'warning', 'danger'])->default('primary');
            $table->timestamps();
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reminders');
    }
}
