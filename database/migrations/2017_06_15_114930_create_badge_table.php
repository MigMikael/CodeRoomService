<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBadgeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badge', function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->integer('image')->unsigned();
            $table->integer('course_id')->unsigned();
            $table->string('type');
            $table->integer('criteria');
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')
                ->on('course')
                ->onDelete('cascade');

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
        Schema::dropIfExists('badge');
    }
}
