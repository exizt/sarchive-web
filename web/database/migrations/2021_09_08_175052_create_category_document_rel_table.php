<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryDocumentRelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sa_category_document_rel', function (Blueprint $table) {
            $table->unsignedBigInteger('archive_id')->comment('아카이브의 ID');
            $table->string('category_name')->default('')->comment('카테고리 태그명');
            $table->unsignedBigInteger('document_id')->comment('문서의 ID');
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['archive_id', 'category_name', 'document_id'], 'pk_idx_sa_category_document_rel');

            $table->index(['document_id'], 'idx_categories_by_document');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sa_category_document_rel');
    }
}
