<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryRelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sa_category_rel', function (Blueprint $table) {
            $table->unsignedBigInteger('archive_id')->comment('아카이브의 ID');
            $table->string('category_name')->default('')->comment('카테고리 태그명');
            $table->string('child_category_name')->default('')->comment('카테고리 태그명');
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['archive_id', 'category_name', 'child_category_name'], 'pk_idx_sa_category_rel');

            $table->index(['archive_id', 'child_category_name'], 'idx_categories_child');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sa_category_rel');
    }
}
