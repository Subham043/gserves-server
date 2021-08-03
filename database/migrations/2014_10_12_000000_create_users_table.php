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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('whatsapp_phone')->nullable();
            $table->string('social_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('email_verified')->default(1);
            $table->timestamp('phone_verified_at')->nullable();
            $table->integer('phone_verified')->default(0);
            $table->string('password')->nullable();
            $table->string('otp');
            $table->integer('is_admin')->default(0);
            $table->integer('is_staff')->default(0);
            $table->rememberToken();
            $table->timestamp('created_at')->nullable(true)->useCurrent();
            $table->timestamp('updated_at')->nullable(true)->useCurrent();
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
