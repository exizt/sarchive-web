<?php

namespace App\Models\SArchive;

use Illuminate\Database\Eloquent\Model;

class SAFolder extends Model
{
    // 테이블명
    protected $table = 'sa_folders';
    // 대량 처리를 위한 허용된 컬럼 설정
    protected $fillable = ['archive_id','name','comments', 'parent_id',
        'index','depth','path'];

    public function getSubBoardList($boardId=0, $depth = 1){

    }

    /**
     * Archive 조회
     */
    public function archive(){
        return $this->belongsTo('App\Models\SArchive\SAArchive', 'archive_id');
    }
}