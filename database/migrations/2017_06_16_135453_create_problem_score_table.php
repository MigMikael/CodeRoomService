<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_score', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('analysis_id')->unsigned();
            $table->float('class')->default(0);
            $table->float('package')->default(0);
            $table->float('enclose')->default(0);
            $table->float('extends')->default(0);;
            $table->float('implements')->default(0);;
            $table->timestamps();

            $table->foreign('analysis_id')
                ->references('id')
                ->on('problem_analysis')
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
        Schema::dropIfExists('problem_score');
    }
}
