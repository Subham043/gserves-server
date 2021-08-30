<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('choices', function (Blueprint $table) {
            $table->id();
            $table->string('choice');
            $table->integer('user_id');
            $table->bigInteger('form_field_id')->unsigned();
            // $table->foreign('form_field_id')->references('id')->on('form__fields')->onDelete('cascade');
            //alter table `choices` add constraint `choices_form_field_id_foreign` foreign key (`form_field_id`) references `form__fields` (`id`) on delete cascade
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
        Schema::dropIfExists('choices');
    }
}
