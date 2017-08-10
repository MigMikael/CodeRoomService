<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnounceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->string('title');
            $table->text('content');
            $table->integer('priority');
            $table->string('show')->default('false');
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')
                ->on('course')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement');
    }
}
