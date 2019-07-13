<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemConstructorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_constructor', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('analysis_id')->unsigned();
            $table->string('access_modifier');
            $table->string('name');
            $table->string('parameter');
            $table->float('score')->default(0);

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
        Schema::dropIfExists('problem_constructor');
    }
}
