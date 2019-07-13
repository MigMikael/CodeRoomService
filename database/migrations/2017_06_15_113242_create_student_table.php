<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student', function (Blueprint $table)
        {
            $table->increments('id');
            $table->string('student_id')->unique();
            $table->string('name');
            $table->string('email');
            $table->integer('image')->unsigned();
            $table->string('token')->unique();
            $table->ipAddress('ip')->nullable();
            $table->string('status');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student');
    }
}
