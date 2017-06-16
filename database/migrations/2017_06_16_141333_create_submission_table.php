<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submission', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->integer('problem_id')->unsigned();
            $table->integer('sub_num')->default(0);
            $table->string('is_accept')->default('false');
            $table->timestamps();

            $table->foreign('student_id')
                ->references('id')
                ->on('student')
                ->onDelete('cascade');
            $table->foreign('problem_id')
                ->references('id')
                ->on('problem');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submission');
    }
}
