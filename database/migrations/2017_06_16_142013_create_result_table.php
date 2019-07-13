<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('submission_file_id')->unsigned();
            $table->string('class');
            $table->string('package');
            $table->string('enclose');
            $table->string('extends');
            $table->string('implements');
            $table->timestamps();

            $table->foreign('submission_file_id')
                ->references('id')
                ->on('submission_file')
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
        Schema::dropIfExists('result');
    }
}
