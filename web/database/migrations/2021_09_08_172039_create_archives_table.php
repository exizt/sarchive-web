<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sa_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('아카이브의 소유 User id');
            $table->string('name')->default('')->comment('아카이브명');
            $table->text('comments')->nullable()->comment('부가 설명');
            $table->unsignedInteger('index')->nullable()->comment('정렬 순서');
            $table->string('route')->nullable()->comment('호출 Route id');
            $table->boolean('is_default')->default(0);
            $table->timestamps();
            // $table->timestamp('deleted_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sa_archives');
    }
}
