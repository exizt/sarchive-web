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
     * 모델의 pathsArray 를 반환
     */
    public function paths_array(){
        $paths = explode('/', rtrim($this->attributes['system_path'], '/'));
        return $paths;
    }

    /**
     * path 목록 조회
     */
    public function paths(){
        $paths = $this->paths_array();

        $result = SAFolder::select(['id','name'])
                ->whereIn('id', $paths )
                ->orderBy('depth', 'asc')
                ->get();
        return $result;
    }

    public static function getRootsByArchive($archiveId, $cols){
        $query = SAFolder::where('depth','1')
        ->where('archive_id', $archiveId);

        if(!empty($cols)){
            $query = $query->select($cols);
        }
        return $query->orderBy('index','asc')->get();
    }

    public static function getChildFolders($folderId, $cols){
        $query = SAFolder::where('parent_id', $folderId);

        if(!empty($cols)){
            $query = $query->select($cols);
        }

        return $query->orderBy('index','asc')->get();
    }

    /**
     * Archive 테이블 조인 조회
     */
    public function archive(){
        return $this->belongsTo('App\Models\SArchive\SAArchive', 'archive_id');
    }
}