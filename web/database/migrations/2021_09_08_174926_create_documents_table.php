<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sa_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('')->comment('글 제목');
            $table->longText('content')->comment('글 본문');
            $table->string('reference')->nullable()->comment('레퍼런스 (url 등)');
            $table->string('summary_var')->nullable()->comment('글 요약');
            $table->unsignedInteger('archive_id')->comment('속한 아카이브 id');
            $table->unsignedInteger('folder_id')->nullable()->comment('폴더의 id');
            $table->string('category')->nullable()->comment('카테고리 Tag');
            $table->timestamps();
            $table->softDeletes();

            // $table->index(['folder_id']);
            $table->index(['folder_id', 'created_at'], 'idx_documents_folder_latest');
            $table->index(['archive_id', 'created_at'], 'idx_documents_archive_latest');

        });

        // FullText Search 를 위한 부분
        DB::statement('ALTER TABLE sa_documents ADD FULLTEXT fulltext_index(title, content)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sa_documents');
    }
}
