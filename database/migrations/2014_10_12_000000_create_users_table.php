<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('username', 30)->unique();
            $table->string('email', 50);
            $table->string('phone', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->string('password');
            $table->enum('roles', ['HELPDESK', 'ADMIN'])->default('ADMIN');
            $table->enum('active', ['Y', 'N'])->default('Y');
            $table->string('api_token', 60)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
