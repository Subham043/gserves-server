<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubServiceFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub__service__form__fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_name');
            $table->string('display_name');
            $table->string('field_column_name');
            $table->string('field_type');
            $table->integer('length')->nullable();
            $table->string('dependent_field_name')->nullable();
            $table->string('operator')->nullable();
            $table->string('operated_value')->nullable();
            $table->integer('mandatory')->default(0);
            $table->integer('order_number');
            $table->integer('status')->default(1);
            $table->string('stored')->default("yes");
            $table->string('storage_table_name');
            $table->bigInteger('sub_service_id')->unsigned();
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
        Schema::dropIfExists('sub__service__form__fields');
    }
}
