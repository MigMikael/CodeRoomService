<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submission_file', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('submission_id')->unsigned();
            $table->string('package');
            $table->string('filename');
            $table->string('mime');
            $table->text('code');

            $table->foreign('submission_id')
                ->references('id')
                ->on('submission')
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
        Schema::dropIfExists('submission_file');
    }
}
