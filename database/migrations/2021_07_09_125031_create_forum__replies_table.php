<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forum__replies', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('forum_id')->unsigned();
            $table->bigInteger('service_id')->unsigned();
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
        Schema::dropIfExists('forum__replies');
    }
}
