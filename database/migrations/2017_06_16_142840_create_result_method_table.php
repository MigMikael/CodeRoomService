<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultMethodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_method', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('result_id')->unsigned();
            $table->string('access_modifier');
            $table->string('non_access_modifier');
            $table->string('return_type');
            $table->string('name');
            $table->string('parameter');
            $table->string('recursive');
            $table->string('loop');
            $table->float('score')->default(0);

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
        Schema::dropIfExists('result_method');
    }
}
