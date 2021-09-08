<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sa_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('사용자의 id');
            $table->string('title')->default('')->comment('글 제목');
            $table->longText('content')->comment('글 본문');
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
        Schema::dropIfExists('sa_pages');
    }
}
