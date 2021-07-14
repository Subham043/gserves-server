<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubServiceFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub__service__fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_name')->unique();
            $table->string('field_type');
            $table->integer('status')->default(1);;
            $table->integer('sub_service_id');
            $table->integer('service_id');
            $table->integer('user_id');
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
        Schema::dropIfExists('sub__service__fields');
    }
}
