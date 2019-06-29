<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSKUsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sku_table', function (Blueprint $table) {
            $table->increments('id');
            $table->char('prefix',5);
            $table->char('depth_1',3);
            $table->char('depth_2',3);
            $table->char('depth_3',3);
            $table->char('depth_4',3);
            $table->char('depth_5',7);
            $table->string('product_name');
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
        Schema::dropIfExists('sku_table');
    }
}
