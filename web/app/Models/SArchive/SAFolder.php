<?php

namespace App\Models\SArchive;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SAFolder extends Model
{
    // 테이블명
    protected $table = 'sa_folders';
    // 대량 처리를 위한 허용된 컬럼 설정
    protected $fillable = ['archive_id','name','comments', 'parent_id',
        'index','depth'];


    /**
     * Archive 테이블 조인 조회
     */
    public function archive(){
        return $this->belongsTo('App\Models\SArchive\SAArchive', 'archive_id');
    }


    /**
     * 모델의 pathsArray 를 반환
     */
    public function paths_array(){
        $paths = explode('/', trim($this->attributes['system_path'], '/'));
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

    /**
     * archiveId 기준으로 최상단 폴더를 조회
     */
    public static function getRootsByArchive($archiveId, $cols){
        $query = SAFolder::where('depth','1')
        ->where('archive_id', $archiveId);

        if(!empty($cols)){
            $query = $query->select($cols);
        }
        return $query->orderBy('index','asc')->get();
    }

    /**
     * folderId 기준으로 바로 하위 폴더를 조회
     */
    public static function getChildFolders($folderId, $cols){
        $query = SAFolder::where('parent_id', $folderId);

        if(!empty($cols)){
            $query = $query->select($cols);
        }

        return $query->orderBy('index','asc')->get();
    }

    /**
     * systemPath 생성
     */
    public static function generateSystemPath($folderId){
        return '/'.$folderId.'/';
    }

    /**
     * systemPath 생성 (parentSystemPath 가 있을 때)
     */
    public static function generateSystemPathAppend($folderId, $parentSystemPath){
        if(empty($parentSystemPath)) return self::generateSystemPath($folderId);
        else return $parentSystemPath.$folderId.'/';
    }

    /**
     * folderId 기준으로 doc_count 와 doc_count_all 을 갱신
     */
    public static function updateDocCountAll($folderId){

        /**
         * folderId 기준으로 doc_count 갱신
         */
        // folderId 유효성 검증 및 조회
        $folder = SAFolder::findOrFail($folderId);
        // doc_count 조회 및 갱신
        $count = SADocument::where('folder_id', $folderId)->count();
        $folder->doc_count = $count;
        $folder->save();

        /**
         * folderId 기준으로 상위폴더(기준 폴더 포함)를 탐색하면서 doc_count_all 갱신
         *
         * 현재 폴더의 system_path 를 / 기준으로 나누면 제각기 상위 노드의 id이다.
         * 이것을 기준으로 상위 폴더들(자신도 포함)의 doc_count_all을 갱신한다.
         */
        /* 쿼리
        update sa_folders
          inner join (select id, name, system_path, (select count(*) from sa_documents as doc inner join sa_folders as p1 on doc.folder_id = p1.id
          where left(p1.system_path, length(sa_folders.system_path)) = sa_folders.system_path) as count
          from sa_folders) as d
        on d.id = sa_folders.id
        set sa_folders.doc_count_all = d.count
        */
        $paths = $folder->paths_array();
        if(count($paths) > 1){
            SAFolder::join(DB::raw('(select id, name, system_path,
                    (select count(*) from sa_documents as doc inner join sa_folders as p1 on doc.folder_id = p1.id
                    where left(p1.system_path, length(sa_folders.system_path)) = sa_folders.system_path) as count
                from sa_folders) as d'),'d.id','=','sa_folders.id')
            ->whereIn('sa_folders.id', $folder->paths_array())
            ->update(['sa_folders.doc_count_all'=> DB::raw('d.count')]);
        }
    }
}
