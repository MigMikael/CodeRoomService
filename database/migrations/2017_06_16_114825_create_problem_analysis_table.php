<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemAnalysisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_analysis', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('problem_file_id')->unsigned();
            $table->string('class');
            $table->string('package');
            $table->string('enclose');
            $table->string('extends');
            $table->string('implements');
            $table->timestamps();

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
        Schema::dropIfExists('problem_analysis');
    }
}
