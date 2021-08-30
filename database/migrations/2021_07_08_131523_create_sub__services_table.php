<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub__services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('tag_line');
            $table->string('storage_table_name')->unique();
            $table->bigInteger('city')->unsigned();
            $table->string('output');
            $table->integer('option_online')->default(0);
            $table->integer('option_person')->default(0);
            $table->integer('option_representative')->default(0);
            $table->string('time_taken');
            $table->string('tracking_url')->unique();
            $table->integer('govt_fees');
            $table->integer('other_expenses');
            $table->integer('service_charges');
            $table->bigInteger('service_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
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
        Schema::dropIfExists('sub__services');
    }
}
