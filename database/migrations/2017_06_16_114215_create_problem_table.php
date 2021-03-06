<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('lesson_id')->unsigned();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('evaluator');
            $table->integer('order');
            $table->integer('question')->nullable();
            $table->float('timelimit')->default('1');
            $table->float('memorylimit')->default('32');
            $table->string('is_parse')->default('false');
            $table->float('score')->default(0);
            $table->string('status');
            $table->timestamps();

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lesson')
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
        Schema::dropIfExists('problem');
    }
}
