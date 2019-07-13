<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('problem_id')->unsigned();
            $table->integer('file_id')->unsigned();
            $table->string('visible');

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
        Schema::dropIfExists('resource');
    }
}
