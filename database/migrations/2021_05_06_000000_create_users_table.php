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
            $table->increments('id');
            $table->string('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('gender')->nullable();
            $table->string('is_verified_email')->nullable();
            $table->string('is_verified_phone')->nullable();
            $table->string('secret_key')->nullable();
            $table->bigInteger('mobile_number')->unique()->nullable();
            $table->Integer('type')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('password')->nullable();
            $table->string('status')->nullable();            
            $table->Datetime('deleted_at')->nullable();
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
