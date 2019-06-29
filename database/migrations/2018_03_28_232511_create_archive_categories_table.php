<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchiveCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archive_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('카테고리 이름');
            $table->integer('parent_id')->unsigned()->index()->comment('상위 ID');
            $table->integer('count')->unsigned()->comment('글 갯수')->default('0');
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
        Schema::dropIfExists('archive_categories');
    }
}
