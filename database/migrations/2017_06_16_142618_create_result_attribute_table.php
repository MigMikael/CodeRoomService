<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_attribute', function (Blueprint $table)
        {
            $table->increments('id');
            $table->integer('result_id')->unsigned();
            $table->string('access_modifier');
            $table->string('non_access_modifier');
            $table->string('data_type');
            $table->string('name');
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
        Schema::dropIfExists('result_attribute');
    }
}
