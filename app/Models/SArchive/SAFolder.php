<?php

namespace App\Models\SArchive;

use Illuminate\Database\Eloquent\Model;

class SAFolder extends Model
{
    // 테이블명
    protected $table = 'sa_folders';
    // 대량 처리를 위한 허용된 컬럼 설정
    protected $fillable = ['archive_id','name','comments', 'parent_id',
        'index','depth'];

    /**
     * path 목록 조회
     */
    public function paths(){
        $paths = explode('/', $this->attributes['system_path']);

        $result = SAFolder::select(['id','name'])
                ->whereIn('id', $paths )
                ->orderBy('depth', 'asc')
                ->get();
        return $result;
    }

    /**
     * Archive 조회
     */
    public function archive(){
        return $this->belongsTo('App\Models\SArchive\SAArchive', 'archive_id');
    }
}