<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('글 제목');
            $table->longText('content')->comment('글 본문');
            $table->text('summary')->comment('글 요약');
            $table->char('unit_code',1)->comment('D:개발 관련, N:일반 / category 테이블 연결이 손실될 경우를 대비한 값')->default('D');
            $table->timestamps();
        });
        
        DB::statement('ALTER TABLE archives ADD FULLTEXT fulltext_index (title,content)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archives');
    }
}
