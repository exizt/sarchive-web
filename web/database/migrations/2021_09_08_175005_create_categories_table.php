<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sa_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('archive_id')->nullable()->comment('아카이브의 ID');
            $table->string('name')->nullable()->comment('카테고리 태그명');
            $table->text('comments')->nullable()->comment('부가 설명');
            $table->string('category')->nullable()->comment('상위 카테고리 태그명');
            $table->string('redirect')->nullable()->comment('리다이렉트가 필요할 때');
            $table->timestamps();

            $table->index(['archive_id', 'name'], 'idx_categories_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sa_categories');
    }
}
