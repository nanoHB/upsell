<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_view', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('offer_id');
            $table->unsignedInteger('shop_id');
            $table->dateTime('created_at');
        });
        Schema::table('report_view',function (Blueprint $table){
            $table->foreign('offer_id')->references('id')
                ->on('offer')->onDelete('cascade');
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
        Schema::dropIfExists('report_view');
    }
}
