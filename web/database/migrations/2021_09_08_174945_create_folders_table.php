<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sa_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('archive_id')->comment('아카이브의 ID');
            $table->string('name')->default('')->comment('폴더명');
            $table->text('comments')->nullable()->comment('부가 설명');
            $table->unsignedBigInteger('parent_id')->comment('상위 폴더 id');
            $table->unsignedInteger('index')->default(0)->comment('정렬 순서');
            $table->unsignedInteger('depth')->nullable()->comment('깊이');
            $table->unsignedInteger('doc_count')->default(0)->comment('글 수');
            $table->unsignedInteger('doc_count_all')->default(0)->comment('총 글 수');
            $table->string('system_path')->nullable()->comment('폴더 경로');
            $table->timestamps();

            $table->index(['archive_id', 'index'], 'idx_folders_index');
            $table->index(['parent_id'], 'idx_folders_parent');
            $table->index(['system_path'], 'idx_folders_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sa_folders');
    }
}
