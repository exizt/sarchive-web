<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoftwareProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('software_product_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('software_product_id')->unsigned()->comment('software_products와 fk')->index();
            $table->string('image_file')->comment('이미지 경로')->nullable();
            $table->string('image_link')->comment('이미지 링크')->nullable();
            $table->integer('order')->comment('순서')->nullable();
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
        Schema::dropIfExists('software_product_images');
    }
}
