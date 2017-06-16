<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentLessonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_lesson', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->integer('lesson_id')->unsigned();
            $table->float('progress');
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('student')
                ->onDelete('cascade');
            $table->foreign('lesson_id')
                ->references('id')
                ->on('lesson')
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
        Schema::dropIfExists('student_lesson');
    }
}
