<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intel', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('confirmed');
            $table->bigInteger('deaths');
            $table->bigInteger('recovered');
            $table->bigInteger('suspected')->nullable();
            $table->text('country');
            $table->text('state')->nullable();
            $table->unsignedTinyInteger('source');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intel');
    }
}
