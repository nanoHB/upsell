<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_sale', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('offer_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('shop_id');
            $table->decimal('amount',15,4);
            $table->string('tracking_code',255);
            $table->boolean('is_purchase');
            $table->dateTime('created_at');
        });
        Schema::table('report_sale',function (Blueprint $table){
            $table->foreign('offer_id')->references('id')
                ->on('offer')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')
                ->on('shops')->onDelete('cascade');
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
        Schema::dropIfExists('report_sale');
    }
}
