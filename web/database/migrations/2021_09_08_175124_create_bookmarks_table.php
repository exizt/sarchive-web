<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookmarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sa_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_bookmark')->default(0);
            $table->boolean('is_favorite')->default(0);
            $table->unsignedBigInteger('archive_id_weak')->comment('문서 id');
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
        Schema::dropIfExists('sa_bookmarks');
    }
}
