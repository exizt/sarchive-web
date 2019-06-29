<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoftwareProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('software_products', function (Blueprint $table) {
            $table->increments('id')->comment('키');
            $table->string('software_sku')->comment('소프트웨어 구분 아이디. 보통 SKU 를 활용.');
            $table->string('software_uri')->comment('url 에서 이용될 식별자')->nullable();
            $table->string('software_name')->comment('소프트웨어 명칭')->nullable();
            $table->string('software_name_en')->comment('소프트웨어 명칭 (영문)')->nullable();
            $table->string('subject')->comment('표시될 제목')->nullable();
            $table->text('description')->comment('짧은 설명')->nullable();
            $table->longText('contents')->comment('본문')->nullable();
            $table->longText('contents_markdown')->comment('본문 Markdown')->nullable();
            $table->string('version_latest')->comment('최근 버전 번호')->nullable();
            $table->string('download_link')->comment('다운로드 링크')->nullable();
            $table->string('download_file')->comment('다운로드 파일 업로드시')->nullable();
            $table->string('preview_link')->comment('미리보기 URL')->nullable();
            $table->string('preview_file')->comment('미리보기 파일 업로드시')->nullable();
            $table->string('external_link')->comment('사이트 내 다른 링크, 외부 사이트의 링크 를 위한 주소')->nullable();
            $table->string('github_user_id')->comment('GitHub > 유저 아이디')->nullable();
            $table->string('github_repo_name')->comment('GitHub > 저장소 명칭')->nullable();
            $table->string('appstore_link')->comment('스토어 링크')->nullable();
            $table->longText('privacy_statement')->comment('개인정보 처리방침')->nullable();
            $table->string('product_type')->comment('유형 구분')->nullable();
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
        Schema::dropIfExists('software_products');
    }
}
