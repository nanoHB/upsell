<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trigger_collection', function (Blueprint $table) {
            $table->unsignedInteger('offer_id');
            $table->unsignedInteger('product_id');
        });
        Schema::table('trigger_collection',function (Blueprint $table){
            $table->primary(['offer_id','product_id']);
            $table->foreign('offer_id')->references('id')
                ->on('offer')->onDelete('cascade');
            $table->foreign('product_id')->references('id')
                ->on('product')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trigger_collection');
    }
}
