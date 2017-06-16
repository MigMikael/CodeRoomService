<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemInputTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_input', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('problem_file_id')->unsigned();
            $table->integer('version');
            $table->string('filename');
            $table->text('content');

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
        Schema::dropIfExists('problem_input');
    }
}
