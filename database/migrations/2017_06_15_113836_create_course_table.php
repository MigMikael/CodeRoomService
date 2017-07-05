<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->integer('image')->unsigned();
            $table->string('color');
            $table->string('token');
            $table->string('status');
            $table->string('mode');
            $table->timestamps();

            $table->foreign('image')
                ->references('id')
                ->on('file')
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
        Schema::dropIfExists('course');
    }
}
