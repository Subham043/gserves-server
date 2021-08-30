<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form__fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_name')->unique();
            $table->string('display_name');
            $table->string('field_type');
            // $table->string('field_column_name');
            // $table->string('dependent_field_id')->nullable();
            // $table->string('operator')->nullable();
            // $table->string('operated_value')->nullable();
            // $table->integer('mandatory')->default(0);
            $table->integer('length')->nullable();
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('form__fields');
    }
}
