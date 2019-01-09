<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path',255);
            $table->text('value');
            $table->unsignedInteger('shop_id');
            $table->timestamps();
        });
        Schema::table('config_data',function (Blueprint $table){
            $table->foreign('shop_id')->references('id')
                ->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config_data');
    }
}
