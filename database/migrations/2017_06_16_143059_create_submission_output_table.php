<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionOutputTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submission_output', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('submission_file_id')->unsigned();
            $table->text('content');
            $table->float('score');
            $table->string('error');

            $table->foreign('submission_file_id')
                ->references('id')
                ->on('submission_file')
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
        Schema::dropIfExists('submission_output');
    }
}
