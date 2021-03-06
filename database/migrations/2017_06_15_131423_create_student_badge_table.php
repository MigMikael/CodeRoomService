<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentBadgeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_badge', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->integer('badge_id')->unsigned();
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('student')
                ->onDelete('cascade');
            $table->foreign('badge_id')
                ->references('id')
                ->on('badge')
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
        Schema::dropIfExists('student_badge');
    }
}
