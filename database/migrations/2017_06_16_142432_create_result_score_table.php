<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_score', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('result_id')->unsigned();
            $table->float('class')->default(0);
            $table->float('package')->default(0);
            $table->float('enclose')->default(0);
            $table->float('extends')->default(0);
            $table->float('implements')->default(0);

            $table->foreign('result_id')
                ->references('id')
                ->on('result')
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
        Schema::dropIfExists('result_score');
    }
}
