<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_course', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->unsigned();
            $table->integer('course_id')->unsigned();
            $table->string('status');
            $table->timestamps();

            $table->foreign('teacher_id')
                ->references('id')
                ->on('teacher')
                ->onDelete('cascade');
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
        Schema::dropIfExists('teacher_course');
    }
}
