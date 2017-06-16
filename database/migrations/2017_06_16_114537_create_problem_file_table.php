<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_file', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('problem_id')->unsigned();
            $table->string('package');
            $table->string('filename');
            $table->string('mime');
            $table->mediumText('code');

            $table->foreign('problem_id')
                ->references('id')
                ->on('problem')
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
        Schema::dropIfExists('problem_file');
    }
}
