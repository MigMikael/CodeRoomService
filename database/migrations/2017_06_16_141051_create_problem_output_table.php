<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemOutputTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_output', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('problem_file_id')->unsigned();
            $table->integer('version');
            $table->string('filename');
            $table->text('content');
            $table->float('score');

            $table->foreign('problem_file_id')
                ->references('id')
                ->on('problem_file')
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
        Schema::dropIfExists('problem_output');
    }
}
