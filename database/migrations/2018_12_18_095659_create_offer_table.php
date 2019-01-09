<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trigger_place',100);
            $table->string('name',100);
            $table->string('type',100);
            $table->string('title',100);
            $table->boolean('active')->default(false);
            $table->text('description')->nullable(true);
            $table->unsignedInteger('shop_id');
            $table->date('start_day');
            $table->date('end_day');
            $table->timestamps();
        });
        Schema::table('offer',function (Blueprint $table){
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
        Schema::dropIfExists('offer');
    }
}
